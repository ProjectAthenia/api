<?php
declare(strict_types=1);

namespace App\Athenia\Services;

use App\Athenia\Contracts\Services\ArchiveHelperServiceContract;
use Illuminate\Filesystem\FilesystemAdapter;
use ZipArchive;

class ArchiveHelperService implements ArchiveHelperServiceContract
{
    /**
     * @param ZipArchive $zipArchive
     */
    public function __construct(private ZipArchive $zipArchive) {}

    /**
     * Unzips the archive, and returns the path to the archive
     *
     * @param FilesystemAdapter $filesystem
     * @param string $path
     * @return string
     */
    public function unzipArchive(FilesystemAdapter $filesystem, string $path): string
    {
        $realArchivePath = $filesystem->path($path);
        $result = $this->zipArchive->open($realArchivePath);

        if ($result !== true) {
            throw new \RuntimeException('Error unzipping archive: Result - ' . $result);
        }

        $targetPath = basename($path, '.zip');
        $realTempPath = $filesystem->path($targetPath);
        $this->zipArchive->extractTo($realTempPath);

        return $targetPath . '/';
    }
}