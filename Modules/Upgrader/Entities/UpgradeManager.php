<?php

namespace Modules\Upgrader\Entities;

use Modules\Upgrader\Entities\Handler\FilesystemWritable;
use Modules\Upgrader\Entities\Handler\Contractor;
use Illuminate\Support\Facades\{
    Artisan, Config, File, Log
};
use ZipArchive;

class UpgradeManager extends Contractor
{
    public const STATE_FILE = 'upgrade.state.json';

    public const PHASE_FILES_DONE = 'files_done';

    /**
     * @var array<int, array{step: string, message: string, level: string, time: string}>
     */
    private array $logs = [];

    /**
     * @var array<int, string>
     */
    private array $directoriesNeedPermission = [];

    /**
     * @return array{success: bool, logs: array, message?: string}
     */
    public function runFilesPhase(string $token): array
    {
        $this->resetLogs();

        $isValid = $this->isValid();

        if (! $isValid['status']) {
            $this->logStep('validate', $isValid['message'], 'error');

            return $this->phaseResponse(false, $isValid['message']);
        }

        if (! $this->validateToken($token)) {
            $this->logStep('validate', __('Invalid or expired upgrade session. Please start again from the update page.'), 'error');

            return $this->phaseResponse(false, __('Invalid upgrade session.'));
        }

        try {
            if (function_exists('beforeUpgrade')) {
                ob_start();

                try {
                    beforeUpgrade();
                } finally {
                    ob_end_clean();
                }
            }

            $this->logStep(
                'start',
                __('Updating files from :from to :to', [
                    'from' => $this->getCurrentVersion(),
                    'to' => $this->getLastVersion(),
                ]),
                'info'
            );

            $this->installFiles($this->updaterJson['archive']);
            $this->writeStateFile($token);

            $this->logStep('files', __('Files updated successfully. Starting database phase…'), 'success');

            return $this->phaseResponse(true);
        } catch (\Throwable $e) {
            $this->logStep('error', $e->getMessage(), 'error');
            Log::error('Upgrader - Files phase failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            $this->recovery();
            $this->logStep('maintenance', __('Disabling maintenance mode…'), 'info');
            self::disableMaintenanceMode();

            return $this->phaseResponse(false, $e->getMessage());
        }
    }

    /**
     * @return array{success: bool, logs: array, message?: string}
     */
    public function runFinalizePhase(string $token): array
    {
        $this->resetLogs();

        if (! $this->validateToken($token)) {
            $this->logStep('validate', __('Invalid or expired upgrade session.'), 'error');

            return $this->phaseResponse(false, __('Invalid upgrade session.'));
        }

        if (! $this->validateStateFile($token, self::PHASE_FILES_DONE)) {
            $this->logStep('validate', __('File update phase has not completed. Please run the upgrade from the beginning.'), 'error');

            return $this->phaseResponse(false, __('File phase not completed.'));
        }

        return $this->executeFinalize($token, true);
    }

    /**
     * @return array<int, string>
     */
    public static function getMaintenanceExceptUris(): array
    {
        $prefixes = array_unique(array_filter([
            trim((string) env('ADMIN_PREFIX', 'admin'), '/'),
            trim((string) config('adminPrefix', ''), '/'),
            trim((string) config('paymoney.prefix', ''), '/'),
        ]));

        $paths = [
            '*/system-update',
            '*/system-update/*',
        ];

        foreach ($prefixes as $prefix) {
            $paths[] = $prefix.'/system-update';
            $paths[] = $prefix.'/system-update/*';
        }

        $appPath = trim((string) parse_url((string) config('app.url'), PHP_URL_PATH), '/');

        if ($appPath !== '') {
            foreach ($prefixes as $prefix) {
                $paths[] = $appPath.'/'.$prefix.'/system-update';
                $paths[] = $appPath.'/'.$prefix.'/system-update/*';
            }

            $paths[] = $appPath.'/system-update';
            $paths[] = $appPath.'/system-update/*';
        }

        try {
            $routePath = trim(parse_url(route('systemUpdate.upgrade', absolute: false), PHP_URL_PATH) ?? '', '/');

            if ($routePath !== '') {
                $paths[] = $routePath;
                $paths[] = $routePath.'/*';
            }
        } catch (\Throwable $e) {
            // Route may be unavailable during early bootstrap
        }

        return array_values(array_unique($paths));
    }

    /**
     * Patch storage/framework/down so upgrade routes work during maintenance.
     */
    public static function syncMaintenanceExceptPaths(): void
    {
        $downPath = storage_path('framework/down');

        if (! is_file($downPath)) {
            return;
        }

        $data = json_decode(file_get_contents($downPath), true);

        if (! is_array($data)) {
            return;
        }

        $data['except'] = array_values(array_unique(array_merge(
            $data['except'] ?? [],
            self::getMaintenanceExceptUris()
        )));

        file_put_contents($downPath, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Enable maintenance with upgrade routes excluded (finalize phase only).
     */
    public static function enableMaintenanceMode(): void
    {
        $except = self::getMaintenanceExceptUris();

        if (! app()->isDown()) {
            Artisan::call('down', [
                '--retry' => 60,
                '--except' => $except,
            ]);
        }

        self::syncMaintenanceExceptPaths();
    }

    public static function disableMaintenanceMode(): void
    {
        if (app()->isDown()) {
            Artisan::call('up');
        }
    }

    /**
     * @return array{success: bool, logs: array, message?: string}
     */
    private function executeFinalize(string $token, bool $clearSession): array
    {
        try {
            self::bootstrapForDatabaseWork();
            $this->clearApplicationConfigCache();

            $this->logStep('maintenance', __('Enabling maintenance mode…'), 'info');
            self::enableMaintenanceMode();

            $this->logStep('database', __('Running database migrations…'), 'info');
            $this->migrations();

            $this->logStep('seed', __('Running database seeders…'), 'info');
            $this->seeds();

            if (function_exists('afterUpgrade')) {
                afterUpgrade();
            }

            $this->logStep('cleanup', __('Cleaning up temporary files…'), 'info');
            $this->cleanUp();

            $this->logStep('optimize', __('Clearing and optimizing application cache…'), 'info');
            $this->refreshConfig();

            $this->setCurrentVersion($this->updaterJson['version']);

            $this->logStep('maintenance', __('Disabling maintenance mode…'), 'info');
            self::disableMaintenanceMode();

            $this->clearStateFile();

            if ($clearSession) {
                self::clearUpgradeToken();
            }

            $this->logStep(
                'complete',
                __('System successfully updated to :version', ['version' => $this->getLastVersion()]),
                'success'
            );

            return $this->phaseResponse(true);
        } catch (\Throwable $e) {
            $this->logStep('error', $e->getMessage(), 'error');
            Log::error('Upgrader - Finalize phase failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->phaseResponse(false, $e->getMessage());
        }
    }

    /**
     * @return array{success: bool, logs: array, message?: string}
     */
    private function phaseResponse(bool $success, ?string $message = null): array
    {
        $response = [
            'success' => $success,
            'logs' => $this->logs,
        ];

        if ($message !== null) {
            $response['message'] = $message;
        }

        return $response;
    }

    private function resetLogs(): void
    {
        $this->logs = [];
    }

    private function logStep(string $step, string $message, string $level = 'info'): void
    {
        $entry = [
            'step' => $step,
            'message' => $message,
            'level' => $level,
            'time' => now()->format('H:i:s'),
        ];

        $this->logs[] = $entry;
        Log::info('Upgrader - [' . $level . '] ' . $message);
    }

    public function validateUpgradeToken(string $token): bool
    {
        return $this->validateToken($token);
    }

    private function validateToken(string $token): bool
    {
        if ($token === '') {
            return false;
        }

        if (session()->has('upgrader.token') && hash_equals((string) session('upgrader.token'), $token)) {
            return true;
        }

        $stored = self::readStoredUpgradeToken();

        return $stored !== null && hash_equals($stored, $token);
    }

    private static function upgradeTokenStoragePath(): string
    {
        return storage_path('updates/.upgrade-token');
    }

    public static function readStoredUpgradeToken(): ?string
    {
        $path = self::upgradeTokenStoragePath();

        if (! is_file($path)) {
            return null;
        }

        $token = trim((string) file_get_contents($path));

        return $token !== '' ? $token : null;
    }

    private function stateFilePath(): string
    {
        return storage_path('updates/' . self::STATE_FILE);
    }

    private function writeStateFile(string $token): void
    {
        $directory = storage_path('updates');

        if (! is_dir($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        File::put($this->stateFilePath(), json_encode([
            'phase' => self::PHASE_FILES_DONE,
            'token' => $token,
            'target_version' => $this->getLastVersion(),
            'current_version' => $this->getCurrentVersion(),
            'archive' => $this->updaterJson['archive'],
            'completed_at' => now()->toIso8601String(),
        ], JSON_PRETTY_PRINT));
    }

    private function validateStateFile(string $token, string $expectedPhase): bool
    {
        $path = $this->stateFilePath();

        if (! file_exists($path)) {
            return false;
        }

        $state = json_decode(file_get_contents($path), true);

        if (! is_array($state)) {
            return false;
        }

        return ($state['phase'] ?? '') === $expectedPhase
            && hash_equals((string) ($state['token'] ?? ''), $token);
    }

    private function clearStateFile(): void
    {
        if (file_exists($this->stateFilePath())) {
            File::delete($this->stateFilePath());
        }
    }

    private function installFiles(string $filename): void
    {
        $this->logStep('delete', __('Removing obsolete files…'), 'info');
        $this->deleteFilesAndDirectories();

        $this->logStep('copy', __('Copying new application files…'), 'info');

        if ($this->copyArchiveFilesAndDirectories($filename) === false) {
            throw new \RuntimeException(__('Failed to extract the update archive.'));
        }
    }

    private function copyArchiveFilesAndDirectories(string $filename): bool
    {
        try {
            $zip = new ZipArchive();
            $res = $zip->open($this->baseURL . '/' . $filename);

            if ($res !== true) {
                throw new \RuntimeException(__('Could not open update archive.'));
            }

            $zip->extractTo(base_path());
            $zip->close();

            return true;
        } catch (\Throwable $e) {
            $this->logStep('copy', $e->getMessage(), 'error');

            return false;
        }
    }

    protected function deleteFilesAndDirectories(): void
    {
        if (! is_array($this->updaterJson['delete'])) {
            return;
        }

        foreach ($this->updaterJson['delete'] as $deletableItem) {
            if (File::isFile($deletableItem) && File::exists($deletableItem)) {
                File::delete($deletableItem);
            } elseif (File::isDirectory($deletableItem) && File::exists($deletableItem)) {
                File::deleteDirectory($deletableItem);
            }
        }

        $downloadDirectory = storage_path('app' . DIRECTORY_SEPARATOR . 'downloaded-files');

        if (File::isDirectory($downloadDirectory)) {
            File::deleteDirectory($downloadDirectory);
        }
    }

    /**
     * Use a non-database cache driver so Laravel can boot before the cache table exists.
     */
    public static function bootstrapForDatabaseWork(): void
    {
        $configCache = base_path('bootstrap/cache/config.php');

        if (is_file($configCache)) {
            @unlink($configCache);
        }

        Config::set('cache.default', 'array');

        if (app()->bound('cache')) {
            app()->forgetInstance('cache');
            app()->forgetInstance('cache.store');
        }
    }

    private function clearApplicationConfigCache(): void
    {
        $configCache = base_path('bootstrap/cache/config.php');

        if (is_file($configCache)) {
            @unlink($configCache);
            $this->logStep('prepare', __('Application config cache cleared.'), 'info');
        }
    }

    /**
     * @return array<int, string>
     */
    private function resolveMigrationVersions(): array
    {
        $versions = config('paymoney.migration_versions') ?? [];

        if (is_string($versions)) {
            $versions = [$versions];
        }

        $target = $this->getLastVersion();

        if ($target) {
            $versions[] = $target;

            $parts = explode('.', $target);

            if (count($parts) === 3) {
                $versions[] = $parts[0] . '.' . $parts[1] . '.0';
            }
        }

        return array_values(array_unique(array_filter($versions)));
    }

    /**
     * @return array<int, string>
     */
    private function collectMigrationPaths(): array
    {
        $paths = [];
        $currentVersion = $this->getCurrentVersion();

        foreach ($this->resolveMigrationVersions() as $version) {
            if ($currentVersion && version_compare($version, $currentVersion, '<=')) {
                continue;
            }

            $versionFolder = 'v' . str_replace('.', '_', $version);

            $paths[] = implode('/', ['database', 'migrations', 'versions', $versionFolder]);

            foreach (\Nwidart\Modules\Facades\Module::getOrdered() as $module) {
                $paths[] = implode('/', ['Modules', $module->getName(), 'Database', 'Migrations', 'versions', $versionFolder]);
            }
        }

        return array_values(array_unique($paths));
    }

    private function migrations(): void
    {
        $versions = $this->resolveMigrationVersions();

        if (empty($versions)) {
            $this->logStep('migrate', __('No migration versions configured — skipped.'), 'warning');

            return;
        }

        $migrationPaths = $this->collectMigrationPaths();

        $this->logStep(
            'migrate',
            __('Migration versions: :versions', ['versions' => implode(', ', $versions)]),
            'info'
        );

        $ranCount = 0;

        foreach ($migrationPaths as $path) {
            $fullPath = base_path($path);

            if (! is_dir($fullPath)) {
                $this->logStep('migrate', __('Skipped (not found): :path', ['path' => $path]), 'warning');

                continue;
            }

            $files = glob($fullPath . '/*.php') ?: [];

            if ($files === []) {
                $this->logStep('migrate', __('Skipped (no migration files): :path', ['path' => $path]), 'warning');

                continue;
            }

            $this->logStep(
                'migrate',
                __('Migrating :count file(s) in :path', ['count' => count($files), 'path' => $path]),
                'info'
            );

            $this->runMigrate($path);
            $ranCount++;

            $this->logStep('migrate', __('Completed: :path', ['path' => $path]), 'success');
        }

        if ($ranCount === 0) {
            $this->logStep('migrate', __('No migrations to run — skipped.'), 'warning');
        }
    }

    private function runMigrate(string $path): void
    {
        self::bootstrapForDatabaseWork();

        $exitCode = Artisan::call('migrate', [
            '--path' => $path,
            '--force' => true,
        ]);

        $output = trim(Artisan::output());

        if ($output !== '') {
            $this->logStep('migrate', $output, 'info');
        }

        if ($exitCode !== 0) {
            throw new \RuntimeException(
                __('Migration failed for :path', ['path' => $path])
                . ($output !== '' ? ': ' . $output : '')
            );
        }
    }

    private function seeds(): void
    {
        $versions = config('paymoney.seed_versions') ?? [];

        if (is_string($versions)) {
            $versions = [$versions];
        }

        if (empty($versions)) {
            $this->logStep('seed', __('No seed versions configured — skipped.'), 'warning');

            return;
        }

        $ranCount = 0;

        $currentVersion = $this->getCurrentVersion();

        foreach ($versions as $version) {
            
            if ($currentVersion && version_compare($version, $currentVersion, '<=')) {
                continue;
            }

            $versionFolder = 'v' . str_replace('.', '_', $version);
            $seedClasses = [
                'Database\\Seeders\\versions\\' . $versionFolder . '\\DatabaseSeeder',
            ];

            foreach (\Nwidart\Modules\Facades\Module::getOrdered() as $module) {
                $seedClasses[] = 'Modules\\' . $module->getName()
                    . '\\Database\\Seeders\\versions\\' . $versionFolder . '\\DatabaseSeeder';
            }

            foreach ($seedClasses as $class) {
                if (! class_exists($class)) {
                    $this->logStep('seed', __('Skipped (not found): :class', ['class' => $class]), 'warning');

                    continue;
                }

                $this->logStep('seed', __('Seeding: :class', ['class' => class_basename($class)]), 'info');

                $exitCode = Artisan::call('db:seed', [
                    '--class' => $class,
                    '--force' => true,
                ]);

                if ($exitCode !== 0) {
                    throw new \RuntimeException(__('Seeder failed: :class', ['class' => $class]));
                }

                $ranCount++;
                $this->logStep('seed', __('Seeded: :class', ['class' => class_basename($class)]), 'success');
            }
        }

        if ($ranCount === 0) {
            $this->logStep('seed', __('No seeders to run — skipped.'), 'warning');
        }
    }

    private function cleanUp(): void
    {
        if (is_dir($this->tempPath())) {
            File::deleteDirectory($this->tempPath());
        }

        if (is_dir($this->backupPath())) {
            File::deleteDirectory($this->backupPath());
        }

        $updatesPath = storage_path('updates');

        if (is_dir($updatesPath)) {
            File::deleteDirectory($updatesPath);
        }
    }

    private function recovery(): void
    {
        $this->logStep('recovery', __('Attempting to restore from backup…'), 'warning');

        try {
            if (! is_dir($this->backupPath())) {
                $this->logStep('recovery', __('No backup directory found.'), 'warning');

                return;
            }

            foreach (File::allFiles($this->backupPath()) as $file) {
                $filename = $this->processRecoveryFilename((string) $file);
                File::copy($this->backupPath($filename), $this->basePath($filename));
            }

            $this->logStep('recovery', __('Recovery completed.'), 'success');
        } catch (\Throwable $e) {
            $this->logStep('recovery', __('Recovery failed: :error', ['error' => $e->getMessage()]), 'error');
        }
    }

    private function processRecoveryFilename(string $filename): string
    {
        return substr($filename, (strlen($filename) - strlen($this->backupPath()) - 1) * (-1));
    }

    public function refreshConfig(): void
    {
        self::bootstrapForDatabaseWork();

        try {
            Artisan::call('optimize:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            try {
                Artisan::call('cache:clear');
            } catch (\Throwable $e) {
                $this->logStep('optimize', __('Cache table clear skipped (will be available after migrations).'), 'warning');
            }

            $this->logStep('optimize', __('Application cache cleared.'), 'success');
        } catch (\Throwable $e) {
            $this->logStep('optimize', __('Cache clear warning: :error', ['error' => $e->getMessage()]), 'warning');
        }
    }

    private function needPermission(): bool
    {
        $filesystem = new FilesystemWritable(base_path(), [
            'exclude' => [
                'bootstrap/cache',
                'storage',
                'storage/app',
                'storage/app/public',
                'storage/framework',
                'storage/framework/cache',
                'storage/framework/sessions',
                'storage/framework/views',
                'storage/logs',
                'public/uploads',
            ],
        ]);

        if (! $filesystem->isWritable()) {
            $this->directoriesNeedPermission = $filesystem->getNotWritables();

            return true;
        }

        return false;
    }

    /**
     * @return array<string, mixed>
     */
    public function isValid(): array
    {
        if (is_null($this->updaterJson['version']) || is_null($this->updaterJson['archive'])) {
            return [
                'status' => false,
                'message' => __('The update file is not valid.'),
            ];
        }

        if (version_compare($this->getLastVersion(), $this->getCurrentVersion(), '=')) {
            return [
                'status' => false,
                'message' => __('The version you uploaded is the same as the current one (:x)', ['x' => $this->getLastVersion()]),
            ];
        }

        if (version_compare($this->getLastVersion(), $this->getCurrentVersion(), '<')) {
            return [
                'status' => false,
                'message' => __('The version you uploaded (:x) is older than the current one (:y)', ['x' => $this->getLastVersion(), 'y' => $this->getCurrentVersion()]),
            ];
        }

        if (! in_array($this->getCurrentVersion(), $this->getSupportedVersions())) {
            return [
                'status' => false,
                'message' => __('You are on a version (:x) that is not supported by this update.', ['x' => $this->getCurrentVersion()]),
            ];
        }

        if ($this->needPermission()) {
            return [
                'status' => false,
                'needPermission' => true,
                'permissionRequire' => $this->directoriesNeedPermission,
                'message' => __('These directories need writable permission. you need re-change the permission after successfully system update.'),
            ];
        }

        return [
            'status' => true,
            'json' => $this->updaterJson,
            'message' => __('An update version (:x) of :y is available.', ['x' => $this->getLastVersion(), 'y' => env('APP_NAME', 'PayMoney')]),
        ];
    }

    public static function createUpgradeToken(): string
    {
        $token = bin2hex(random_bytes(20));
        session(['upgrader.token' => $token]);

        $directory = storage_path('updates');

        if (! is_dir($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        File::put(self::upgradeTokenStoragePath(), $token);

        return $token;
    }

    public static function clearUpgradeToken(): void
    {
        session()->forget('upgrader.token');

        if (is_file(self::upgradeTokenStoragePath())) {
            File::delete(self::upgradeTokenStoragePath());
        }
    }
}
