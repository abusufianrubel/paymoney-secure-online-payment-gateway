<?php

namespace Modules\Upgrader\Entities\Handler;

class FilesystemWritable
{
    /**
     * The base path to check for writability
     */
    private string $path;

    /**
     * Holds paths that are not writable
     */
    private array $notWritable = [];

    /**
     * Options for writability check.
     *
     * @var array
     *            - mode: Default permission mode to set if needed.
     *            - exclude: Paths to be excluded from the check.
     */
    private array $options = [
        'mode' => 0777,
        'exclude' => [],
    ];

    /**
     * FilesystemWritable constructor.
     *
     * @param  string  $path  The base path to check for writability
     * @param  array  $options  Options for writability check
     */
    public function __construct(string $path, array $options = [])
    {
        $this->path = $path;
        $this->options = array_merge($this->options, $options);
    }

    /**
     * Checks if the specified filesystem path is writable.
     */
    public function isWritable(): bool
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->path),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $currentPath = $item->getPathname();

            if ($this->isItemExcluded($currentPath)) {
                continue;
            }

            if (! $this->isFileOrDirectoryWritable($item)) {
                $this->notWritable[] = $currentPath;
            }
        }

        return empty($this->notWritable);
    }

    /**
     * Retrieves the paths that are not writable.
     */
    public function getNotWritables(): array
    {
        return $this->notWritable;
    }

    /**
     * Checks if a specific path should be excluded from writability check.
     *
     * @param  string  $path  The path to check for exclusion
     */
    private function isItemExcluded(string $path): bool
    {
        foreach ($this->options['exclude'] as $folder) {
            if (strpos($path, $folder) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if a file or directory is writable. If not, tries to make it writable.
     *
     * @param  \SplFileInfo  $item  The file or directory to check
     */
    private function isFileOrDirectoryWritable(\SplFileInfo $item): bool
    {
        if ($item->isDir() || $item->isFile()) {
            if (! is_writable($item->getPathname())) {
                return $this->changePermission($item);
            }

            return true;
        }

        return false;
    }

    /**
     * Changes the permission of a file or directory to the specified mode.
     *
     * @param  \SplFileInfo  $item  The file or directory to change permission
     */
    private function changePermission(\SplFileInfo $item): bool
    {
        try {
            chmod($item->getPathname(), $this->options['mode']);
        } catch (\Exception $e) {
            return false;
        }

        return $item->isWritable();
    }
}
