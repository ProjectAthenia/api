<?php
declare(strict_types=1);

namespace App\Providers;

use App\Athenia\Providers\BaseEventServiceProvider;

/**
 * Class EventServiceProvider
 * @package App\Providers
 */
class EventServiceProvider extends BaseEventServiceProvider
{
    /**
     * Gets all application level event and mappings
     *
     * @return array
     */
    public function getAppListenerMapping(): array
    {
        return [
        ];
    }

    /**
     * Gets all application specific listeners for when a user is merged within the Athenia pipeline
     *
     * @return array
     */
    public function getAppUserMergeListeners(): array
    {
        return [
        ];
    }

    /**
     * Registers any application specific observers
     *
     * @return void
     */
    public function registerObservers(): void
    {
    }
}
