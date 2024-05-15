<?php
declare(strict_types=1);

namespace App\Athenia\Services;

use App\Athenia\Contracts\Services\DirectoryCopyServiceContract;
use Illuminate\Filesystem\FilesystemAdapter;

class DirectoryCopyService implements DirectoryCopyServiceContract
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
    public function copyDirectory(FilesystemAdapter $from, FilesystemAdapter $to, string $fromPath, string $toPath)
    {
        foreach ($from->files($fromPath) as $file) {
            $to->put($toPath . basename($file), $from->get($file));
        }
        foreach ($from->directories($fromPath) as $directory) {
            $target = $toPath . basename($directory);
            $to->createDir($target);
            $this->copyDirectory($from, $to, $directory, $target . '/');
        }
    }
}