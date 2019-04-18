<?php
declare(strict_types=1);

namespace Tests\Unit\Providers;

use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\Str;
use App\Providers\AppServiceProvider;
use Laracasts\Generators\GeneratorsServiceProvider;
use Tests\TestCase;

/**
 * Class AppServiceProviderTest
 * @package Tests\Unit\Providers
 */
class AppServiceProviderTest extends TestCase
{
    /**
     * @dataProvider allProviders
     * @param $provide
     */
    public function testBinds($provide)
    {
        $this->app->make($provide);
    }

    public function testProvidesAll()
    {
        $app = new Application();
        $repositoryProvider = new AppServiceProvider($app);

        $provides = $repositoryProvider->provides();
        $contracts = array_reduce($this->allProviders(), function($carry, $item) {
            $carry[] = $item[0];
            return $carry;
        }, []);

        $this->assertEquals(0, count(array_diff(array_merge($provides, $contracts), array_intersect($provides, $contracts))));
    }

    /**
     * this gets all the repository contracts, and returns them - so we can test making them
     *
     * @return array
     */
    public function allProviders()
    {
        $app = new Application();
        $app['env'] = 'testing';
        $repositoryProvider = new AppServiceProvider($app);
        $repositoryProvider->register();

        $repositoryContracts = [];

        foreach (array_keys($app->getBindings()) as $contract) {
            if (Str::startsWith($contract, 'App\Contracts\Services')) {
                $repositoryContracts[] = [$contract];
            }
        }

        return $repositoryContracts;
    }

    public function testRegisterEnvironmentSpecificProviders()
    {
        $appMock = mock(Application::class);
        $appMock->shouldReceive('environment')->once()->andReturn('local');

        $appMock->shouldReceive('register')->with(GeneratorsServiceProvider::class);
        $appMock->shouldReceive('register')->with(IdeHelperServiceProvider::class);

        $provider = new AppServiceProvider($appMock);
        $provider->registerEnvironmentSpecificProviders();
    }
}