<?php
declare(strict_types=1);

namespace App\Athenia\Services\Asset;

use App\Athenia\Contracts\Services\Asset\AssetConfigurationServiceContract;

class AssetConfigurationService implements AssetConfigurationServiceContract
{
    /**
     * @param string $serverUrl
     * @param string $baseAssetDirectory
     */
    public function __construct(private string $serverUrl, private string $baseAssetDirectory) {}

    /**
     * @return string
     */
    public function getServerUrl(): string
    {
        return $this->serverUrl;
    }

    /**
     * @return string
     */
    public function getBaseAssetDirectory(): string
    {
        return $this->baseAssetDirectory;
    }
}