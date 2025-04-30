<?php
declare(strict_types=1);

namespace App\Providers;

use App\Athenia\Providers\BaseRepositoryProvider;

/**
 * Class AppRepositoryProvider
 * @package App\Providers
 */
class AppRepositoryProvider extends BaseRepositoryProvider
{
    /**
     * All app specific repositories that are provided here
     *
     * @return array
     */
    public function appProviders(): array
    {
        return [];
    }

    /**
     * Gets all morph maps application specific
     *
     * @return array
     */
    public function appMorphMaps(): array
    {
        return [];
    }

    /**
     * Runs any app specific registrations
     *
     * @return void
     */
    public function registerApp(): void
    {
    }
}