<?php
declare(strict_types=1);

namespace App\Athenia\Providers;

use App\Athenia\Contracts\Repositories\User\UserRepositoryContract;
use App\Athenia\Contracts\ThreadSecurity\ThreadSubjectGateProviderContract;
use App\Athenia\Services\UserAuthenticationService;
use App\Athenia\ThreadSecurity\ThreadSubjectGateProvider;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

/**
 * Class AuthServiceProvider
 * @package App\Providers
 */
abstract class BaseAuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     * Normally these will be automatically guessed as long as
     *  the models directory structure matches the policies directory structure.
     * Any exceptions should be set here.
     *
     * @var array
     */
    protected $policies = [
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $this->app->bind(ThreadSubjectGateProviderContract::class, function() {
            return new ThreadSubjectGateProvider($this->app);
        });

        /** @var AuthManager $auth */
        $auth = $this->app->make('auth');

        $auth->provider('user-authentication', function ($app, array $config) {

            /** @var Application $app */
            $userRepository = $app->make(UserRepositoryContract::class);
            $hasher = $app->make(Hasher::class);

            return new UserAuthenticationService($userRepository, $hasher);
        });

        /** @var Gate $gate */
        Gate::guessPolicyNamesUsing([$this, 'guessPolicyName']);
    }

    /**
     * Automatically guesses a policies name based on the app structure
     *
     * @param string $modelClass
     * @return string
     */
    public function guessPolicyName(string $modelClass): string
    {
        return str_replace('Models', 'Policies', $modelClass) . 'Policy';
    }
}
