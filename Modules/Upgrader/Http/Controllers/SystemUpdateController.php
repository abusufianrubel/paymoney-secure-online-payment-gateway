<?php

/**
 * @package SystemUpdateController
 * @author TechVillage <support@techvill.org>
 * @contributor Foisal Ahmed <[foisal.techvill@gmail.com]>
 * @created 20-05-2026
 */

namespace Modules\Upgrader\Http\Controllers;

use Modules\Upgrader\Entities\UpgradeManager;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\libraries\PhpInfo;
use GuzzleHttp\Client;
use Modules\Addons\Entities\Envato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class SystemUpdateController extends Controller
{
    /**
     * Display the resource.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function upgrade(Request $request)
    {
        if ($request->isMethod('get')) {
            if ($request->get('upgrade') === 'run') {
                return $this->showUpgrading($request);
            }

            if ($request->get('process') === 'files') {
                return $this->processFiles($request);
            }

            if ($request->get('process') === 'finalize') {
                return $this->processFinalize($request);
            }

            if ($request->get('eligible') === '1') {
                return $this->showEligibleAfterUpload();
            }

            $data['menu'] = 'system-update';
            $data['applicationVersion'] = config('paymoney.version');

            return view('upgrader::update', $data);
        }

        if ($request->isMethod('post')) {
            if (checkDemoEnvironment()) {
                Common::one_time_message('error', __('System upgrade is not allowed in demo site.'));
                return back();
            }

            $configurationError = $this->getConfigurationError();

            if ($configurationError !== null) {
                Common::one_time_message('error', $configurationError);

                return back();
            }

            $uploadError = $this->getUploadErrorMessage($request);

            if ($uploadError !== null) {
                Common::one_time_message('error', $uploadError);

                return back()->withInput();
            }

            $validator = Validator::make($request->all(), [
                'envatoUsername' => 'required|string',
                'purchaseCode' => 'required|string',
                'attachment' => 'required|file|mimes:zip,rar,7zip',
            ]);

            if ($validator->fails()) {
                Common::one_time_message('error', $validator->errors()->first());

                return back()->withInput();
            }

            $purchaseData = Envato::isValidPurchaseCode($request->purchaseCode, $request->envatoUsername, config('installer.item_id'));

            if (! $purchaseData->status) {
                Common::one_time_message('error', __('Please provide valid purchase code and username.'));

                return back();
            }

            if (! class_exists('ZipArchive')) {
                Common::one_time_message('error', __('Something went wrong please try again.'));

                return back();
            }

            $zip = new ZipArchive;
            $res = $zip->open($request->attachment);

            $upgraderDirecotory = storage_path('updates');

            if (is_dir($upgraderDirecotory)) {
                File::deleteDirectory($upgraderDirecotory);
            }

            if ($res === true) {
                $zip->extractTo($upgraderDirecotory);
                $zip->close();
            }

            UpgradeManager::createUpgradeToken();

            return redirect()->route('systemUpdate.upgrade', ['eligible' => '1']);
        }

        return back();
    }

    /**
     * Show the single-page upgrade progress UI.
     */
    private function showUpgrading(Request $request)
    {
        $token = (string) $request->query('token', '');

        $manager = new UpgradeManager;

        if ($token === '' || ! $manager->validateUpgradeToken($token)) {
            Common::one_time_message('error', __('Invalid upgrade session. Please upload the update package again.'));

            return redirect()->route('systemUpdate.upgrade');
        }

        if (! file_exists(storage_path('updates/updater.json'))) {
            Common::one_time_message('error', __('Update package not found. Please upload again.'));

            return redirect()->route('systemUpdate.upgrade');
        }
        $isValid = $manager->isValid();

        if (! $isValid['status']) {
            Common::one_time_message('error', $isValid['message']);

            return redirect()->route('systemUpdate.upgrade');
        }

        return view('upgrader::upgrading', [
            'menu' => 'system-update',
            'upgradeToken' => $token,
            'currentVersion' => config('paymoney.version'),
            'targetVersion' => $isValid['json']['version'],
            'filesUrl' => route('systemUpdate.upgrade', ['process' => 'files', 'token' => $token]),
            'finalizeUrl' => route('systemUpdate.upgrade', ['process' => 'finalize', 'token' => $token]),
            'homeUrl' => url('/'),
            'retryUrl' => route('systemUpdate.upgrade', ['upgrade' => 'run', 'token' => $token]),
        ]);
    }

    /**
     * Phase 1: delete and copy files.
     */
    private function processFiles(Request $request)
    {
        return $this->runUpgradePhase(function () use ($request) {
            $token = (string) $request->query('token', '');

            return (new UpgradeManager)->runFilesPhase($token);
        });
    }

    /**
     * Phase 2: migrations, seeds, cleanup, optimize.
     */
    private function processFinalize(Request $request)
    {
        return $this->runUpgradePhase(function () use ($request) {
            UpgradeManager::bootstrapForDatabaseWork();

            $token = (string) $request->query('token', '');

            return (new UpgradeManager)->runFinalizePhase($token);
        });
    }

    /**
     * Always return JSON for upgrade phase endpoints (never HTML error pages).
     *
     * @param  callable(): array{success: bool, logs: array, message?: string}  $phase
     */
    private function runUpgradePhase(callable $phase)
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        ob_start();

        try {
            @ini_set('max_execution_time', '900');
            @set_time_limit(900);

            if (session()->isStarted()) {
                session()->save();
            }

            $result = $phase();
            ob_end_clean();

            return response()->json($result, ($result['success'] ?? false) ? 200 : 500);
        } catch (\Throwable $e) {
            ob_end_clean();

            Log::error('Upgrader - Phase request failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'logs' => [],
            ], 500);
        }
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    private function showEligibleAfterUpload()
    {
        if (! file_exists(storage_path('updates/updater.json'))) {
            Common::one_time_message('error', __('Update package not found. Please upload again.'));

            return redirect()->route('systemUpdate.upgrade');
        }

        return $this->eligibleView();
    }

    /**
     * @return \Illuminate\View\View
     */
    private function eligibleView()
    {
        $upgrader = (new UpgradeManager)->isValid();
        $upgradeToken = null;

        if ($upgrader['status']) {
            $upgradeToken = session('upgrader.token');

            if (! is_string($upgradeToken) || $upgradeToken === '') {
                $upgradeToken = UpgradeManager::readStoredUpgradeToken()
                    ?? UpgradeManager::createUpgradeToken();
            }
        }

        return view('upgrader::eligible', compact('upgrader', 'upgradeToken'));
    }

    /**
     * Return a configuration error message, or null if the server is ready for upgrade uploads.
     */
    private function getConfigurationError(): ?string
    {
        $configurations = PhpInfo::phpinfo_configuration();

        if (empty($configurations)) {
            return __('phpinfo() is disabled. Please contact with your hosting provider.');
        }

        $limitsTooLow = [
            (int) str_replace('M', '', $configurations['upload_max_filesize']) < 128,
            (int) $configurations['max_file_uploads'] < 20,
            (int) str_replace('M', '', $configurations['post_max_size']) < 128,
            (int) $configurations['max_execution_time'] < 600,
            (int) $configurations['max_input_time'] < 120,
            (int) $configurations['max_input_vars'] < 1000,
            (int) str_replace('M', '', $configurations['memory_limit']) < 256,
        ];

        if (in_array(true, $limitsTooLow, true)) {
            return __('Please check system configuration, go to :x', ['x' => route('systemInfo.index')]);
        }

        return null;
    }

    /**
     * Return a message when PHP rejected the upload before Laravel validation (e.g. file too large).
     */
    private function getUploadErrorMessage(Request $request): ?string
    {
        if (! $request->hasFile('attachment')) {
            return null;
        }

        $file = $request->file('attachment');

        if ($file->isValid()) {
            return null;
        }

        return match ($file->getError()) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => __('The uploaded file exceeds the server upload size limit.'),
            UPLOAD_ERR_PARTIAL => __('The file was only partially uploaded. Please try again.'),
            UPLOAD_ERR_NO_FILE => __('No file was uploaded.'),
            default => __('The file upload failed. Please try again.'),
        };
    }

    /**
     * Check latest version
     */
    public function checkVersion()
    {
        $itemCode = config('paymoney.item_code');
        $version = config('paymoney.version');
        $serverUrl = config('paymoney.server_url');

        $apiEndpoint = $serverUrl . "/api/v1/version/check/{$itemCode}/{$version}";
        $newVersion = $version;

        try {
            $response = Http::get($apiEndpoint);

            if (! $response->successful() || ! isset($response->json()['response']['records']['version'])) {
                return view(
                    'upgrader::version',
                    [
                        'currentVersion' => $version,
                        'latestVersion' => $newVersion,
                        'status' => 'fail',
                        'menu' => 'system-update',
                        'message' => $response->json()['response']['status']['message'],
                    ]
                );
            }

            $newVersion = $response->json()['response']['records']['version'];

            return view('upgrader::version', [
                'currentVersion' => $version,
                'latestVersion' => $newVersion,
                'status' => 'success',
                'menu' => 'system-update',
                'message' => $response->json()['response']['records']['message'],
            ]);
        } catch (\Exception $e) {
            return view('upgrader::version', [
                'currentVersion' => $version,
                'latestVersion' => $newVersion,
                'status' => 'fail',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Download latest version
     */
    public function downloadVersion(Request $request)
    {
        $itemCode = config('paymoney.item_code');
        $serverUrl = config('paymoney.server_url');
        $apiEndpoint = $serverUrl . '/api/v1/version/download';
        $storagePath = storage_path('app/downloaded-files');

        $client = new Client();

        try {
            $response = $client->post($apiEndpoint, [
                'form_params' => [
                    'item_code' => $itemCode,
                    'version' => $request->version,
                    'domain' => request()->getSchemeAndHttpHost(),
                    'purchase_code' => $request->purchase_code,
                ],
            ]);

            $header = $response->getHeader('Content-Disposition');
            $fileExplode = explode('filename=', $header[0]);
            $fileName = end($fileExplode);

            $response->getBody()->rewind();

            if (! is_dir($storagePath)) {
                mkdir($storagePath, config('app.filePermission'), true);
            }

            file_put_contents($storagePath . '/' . $fileName, $response->getBody());

            $zip = new ZipArchive;
            $res = $zip->open($storagePath . '/' . $fileName);

            $upgraderDirecotory = storage_path('updates');

            if (is_dir($upgraderDirecotory)) {
                File::deleteDirectory($upgraderDirecotory);
            }

            if ($res === true) {
                $zip->extractTo($upgraderDirecotory);
                $zip->close();
            }

            UpgradeManager::createUpgradeToken();

            return redirect()->route('systemUpdate.upgrade', ['eligible' => '1']);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $downloadDirectory = storage_path('app' . DIRECTORY_SEPARATOR . 'downloaded-files');

            if (File::isDirectory($downloadDirectory)) {
                File::deleteDirectory($downloadDirectory);
            }

            Common::one_time_message('error', __('Purchase code not verified'));

            return back();
        }
    }
}
