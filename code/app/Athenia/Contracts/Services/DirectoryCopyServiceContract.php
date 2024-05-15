<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Services;

use Illuminate\Filesystem\FilesystemAdapter;

interface DirectoryCopyServiceContract
{
    /**
     * Copies a directory from one source to another
     *
     * @param FilesystemAdapter $from
     * @param FilesystemAdapter $to
     * @param string $fromPath
     * @param string $toPath
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function copyDirectory(FilesystemAdapter $from, FilesystemAdapter $to, string $fromPath, string $toPath);
}