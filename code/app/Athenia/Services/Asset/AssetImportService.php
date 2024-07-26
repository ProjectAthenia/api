<?php
declare(strict_types=1);

namespace App\Athenia\Services\Asset;

use App\Athenia\Contracts\Models\IsAnEntityContract;
use App\Athenia\Contracts\Repositories\AssetRepositoryContract;
use App\Athenia\Contracts\Services\Asset\AssetImportServiceContract;
use App\Models\Asset;
use GuzzleHttp\Client;

class AssetImportService implements AssetImportServiceContract
{
    /**
     * @param AssetRepositoryContract $assetRepository
     */
    public function __construct(
        private AssetRepositoryContract $assetRepository,
        private Client $client,
    ) {}

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
        try {
            if ($path) {
                $fileInformation = pathinfo($path);

                $response = $this->client->get($url);
                $assetContent = $response->getStatusCode() == 200 ? $response->getBody()->getContents() : null;

                if ($assetContent !== null) {
                    return $this->assetRepository->create([
                        'source' => $url,
                        'file_contents' => $assetContent,
                        'file_extension' => $fileInformation['extension'],
                        'owner_type' => $owner->morphRelationName(),
                        'owner_id' => $owner->id,
                    ]);
                }
            }
        } catch (\Exception $e) {}

        return null;
    }
}
