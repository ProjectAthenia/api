<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Providers;

use App\Providers\AppRepositoryProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class AppRepositoryProviderTest
 * @package Tests\Athenia\Unit\Providers
 */
final class AppRepositoryProviderTest extends TestCase
{
    public function testProvidesAll(): void
    {
        $app = new Application();
        $repositoryProvider = new AppRepositoryProvider($app);

        $provides = $repositoryProvider->provides();
        $contracts = array_reduce($this->allProviders(), function($carry, $item) {
            $carry[] = $item[0];
            return $carry;
        }, []);

        $this->assertEquals(0, count(array_diff(array_merge($provides, $contracts), array_intersect($provides, $contracts))));
    }

    public function allProviders()
    {
        $app = new Application();
        $repositoryProvider = new AppRepositoryProvider($app);
        $repositoryProvider->register();

        $repositoryContracts = [];

        foreach (array_keys($app->getBindings()) as $contract) {
            if (Str::startsWith($contract, 'App\Contracts\Repositories')) {
                $repositoryContracts[] = [$contract];
            }
        }

        return $repositoryContracts;
    }
}