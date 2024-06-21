<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Services\Asset;

interface AssetConfigurationServiceContract
{
    /**
     * Gets the URL for the server where the assets live
     *
     * @return string
     */
    public function getServerUrl(): string;

    /**
     * Gets the directory where all assets live on the server
     *
     * @return string
     */
    public function getBaseAssetDirectory(): string;
}