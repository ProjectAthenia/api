<?php
declare(strict_types=1);

namespace App\Contracts\Services;

use App\Contracts\Models\IsAnEntity;
use App\Models\Asset;

interface AssetImportServiceContract
{
    /**
     * imports an asset from a url and returns the data model
     *
     * @param IsAnEntity $owner
     * @param string $url
     * @return Asset|null
     */
    public function importAsset(IsAnEntity $owner, string $url): ?Asset;
}
