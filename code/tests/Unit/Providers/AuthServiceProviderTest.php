<?php
declare(strict_types=1);

namespace Tests\Unit\Providers;

use App\Contracts\Repositories\User\UserRepositoryContract;
use App\Models\User\User;
use App\Policies\User\UserPolicy;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Hashing\Hasher;
use App\Providers\AuthServiceProvider;
use App\Services\UserAuthenticationService;
use ReflectionClass;
use Tests\TestCase;
use Tests\Traits\ReflectionHelpers;

/**
 * Class AuthServiceProviderTest
 * @package Tests\Unit\Providers
 */
final class AuthServiceProviderTest extends TestCase
{
    use ReflectionHelpers;

    public function getRegisteredPolicies()
    {
        $gate = $this->app->make(Gate::class);
        $reflection = new ReflectionClass($gate);
        $property = $reflection->getProperty('policies');
        $property->setAccessible(true);

        return $property->getValue($gate);
    }

    public function testUserAuthenticationRegistered(): void
    {
        $app = mock(Application::class);

        $app->shouldReceive('make')->once()->with(UserRepositoryContract::class)->andReturn(mock(UserRepositoryContract::class));
        $app->shouldReceive('make')->once()->with(Hasher::class)->andReturn(mock(Hasher::class));

        $auth = mock(AuthManager::class);

        $auth->shouldReceive('provider')->once()->with('user-authentication', \Mockery::on(function($callback) use ($app) {
            $result = $callback($app, []);

            $this->assertInstanceOf(UserAuthenticationService::class, $result);

            return true;
        }));

        $app->shouldReceive('make')->once()->with('auth')->andReturn($auth);
        $app->shouldReceive('bind')->once();

        $provider = new AuthServiceProvider($app);

        $provider->boot();
    }

    public function testGuessPolicyName(): void
    {
        $provider = new AuthServiceProvider(mock(Application::class));

        $this->assertEquals(UserPolicy::class, $provider->guessPolicyName(User::class));
    }
}