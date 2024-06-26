<?php
declare(strict_types=1);

namespace App\Athenia\Services;

use App\Athenia\Contracts\Models\IsAnEntityContract;
use App\Athenia\Contracts\Repositories\AssetRepositoryContract;
use App\Athenia\Contracts\Services\AssetImportServiceContract;
use App\Models\Asset;

class AssetImportService implements AssetImportServiceContract
{
    /**
     * @param AssetRepositoryContract $assetRepository
     */
    public function __construct(private AssetRepositoryContract $assetRepository) {}

    /**
     * imports an asset from a url and returns the data model
     *
     * @param IsAnEntityContract $owner
     * @param string $url
     * @return Asset|null
     * @throws \ImagickException
     */
    public function importAsset(IsAnEntityContract $owner, string $url): ?Asset
    {
        $path = parse_url($url, PHP_URL_PATH);
        $fileInformation = pathinfo($path);

        $assetContent = file_get_contents($url);

        return ($assetContent && isset($fileInformation['extension'])) ? $this->assetRepository->create([
            'source' => $url,
            'file_contents' => $assetContent,
            'file_extension' => $fileInformation['extension'],
            'owner_type' => $owner->morphRelationName(),
            'owner_id' => $owner->id,
        ]) : null;
    }
}
