<?php
declare(strict_types=1);

namespace App\Athenia\Jobs;

use App\Athenia\Contracts\Repositories\AssetRepositoryContract;
use App\Athenia\Contracts\Services\Asset\AssetConfigurationServiceContract;
use App\Models\Asset;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\Factory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Imagick;

class CalculateAssetDimensionsJob implements ShouldQueue
{
    use Queueable;

    /**
     * @param Asset $asset
     */
    public function __construct(private Asset $asset) {}

    /**
     * Calculates our actual dimensions for us
     *
     * @param AssetRepositoryContract $assetRepository
     * @param Factory $fileSystem
     * @param AssetConfigurationServiceContract $assetConfigurationService
     * @return void
     * @throws \ImagickException
     */
    public function handle(
        AssetRepositoryContract $assetRepository,
        Factory $fileSystem,
        AssetConfigurationServiceContract $assetConfigurationService,
    ) {
        $publicDisk = $fileSystem->disk('public');

        $assetDirectory = $assetConfigurationService->getBaseAssetDirectory() . '/';
        $parts = explode(
            $assetDirectory,
            $this->asset->url,
        );
        $fileName = $assetDirectory . end($parts);
        $fileContents = $publicDisk->get($fileName);

        if ($fileContents) {

            $image = new Imagick();
            $image->readImageBlob($fileContents);
            $width = $image->getImageWidth();
            $height = $image->getImageHeight();

            $assetRepository->update($this->asset, [
                'width' => $width,
                'height' => $height,
            ]);
        }
    }
}