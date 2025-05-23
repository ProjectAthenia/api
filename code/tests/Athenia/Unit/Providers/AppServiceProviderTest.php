<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Providers;

use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Application;
use Illuminate\Support\Str;
use App\Providers\AppServiceProvider;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Class AppServiceProviderTest
 * @package Tests\Athenia\Unit\Providers
 */
final class AppServiceProviderTest extends TestCase
{
    #[DataProvider('allProviders')]
    public function testBinds($provide): void
    {
        $this->app->make($provide);
    }

    public function testProvidesAll(): void
    {
        $app = new Application();
        $repositoryProvider = new AppServiceProvider($app);

        $provides = $repositoryProvider->provides();
        $contracts = array_reduce($this->allProviders(), function($carry, $item) {
            $carry[] = $item[0];
            return $carry;
        }, []);

        $misconfigured = array_values(array_diff(array_merge($provides, $contracts), array_intersect($provides, $contracts)));

        $this->assertEmpty($misconfigured, "The following services are misconfigured " . json_encode($misconfigured));
    }

    /**
     * this gets all the repository contracts, and returns them - so we can test making them
     *
     * @return array
     */
    public static function allProviders(): array
    {
        $app = new Application();
        $app['env'] = 'testing';
        $repositoryProvider = new AppServiceProvider($app);
        $repositoryProvider->register();

        $repositoryContracts = [];

        foreach (array_keys($app->getBindings()) as $contract) {
            if (Str::contains($contract, 'Contracts\Services')) {
                $repositoryContracts[] = [$contract];
            }
        }

        return $repositoryContracts;
    }

    public function testRegisterEnvironmentSpecificProviders(): void
    {
        $appMock = mock(Application::class);
        $appMock->shouldReceive('environment')->once()->andReturn('local');

        $appMock->shouldReceive('register')->with(IdeHelperServiceProvider::class);

        $provider = new AppServiceProvider($appMock);
        $provider->registerEnvironmentSpecificProviders();
    }
}