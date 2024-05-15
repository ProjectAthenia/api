<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Services;

use App\Athenia\Contracts\Models\IsAnEntityContract;
use App\Models\Asset;

interface AssetImportServiceContract
{
    /**
     * imports an asset from a url and returns the data model
     *
     * @param IsAnEntityContract $owner
     * @param string $url
     * @return Asset|null
     */
    public function importAsset(IsAnEntityContract $owner, string $url): ?Asset;
}
