<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Services;

use Illuminate\Filesystem\FilesystemAdapter;

interface ArchiveHelperServiceContract
{
    /**
     * Unzips the archive, and returns the path to the archive
     *
     * @param FilesystemAdapter $filesystem
     * @param string $path
     * @return string
     */
    public function unzipArchive(FilesystemAdapter $filesystem, string $path): string;
}