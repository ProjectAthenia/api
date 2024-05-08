<?php
declare(strict_types=1);

namespace App\Providers;

use App\Providers\AtheniaProviders\BaseServiceProvider;

/**
 * Class AppServiceProvider
 * @package App\Providers
 */
class AppServiceProvider extends BaseServiceProvider
{
    /**
     * All app specific repositories that are provided here
     *
     * @return array
     */
    public function appProviders(): array
    {
        return [
        ];
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
