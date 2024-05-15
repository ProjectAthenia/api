<?php
declare(strict_types=1);

namespace App\Athenia\Providers;

use App\Athenia\Contracts\Repositories\AssetRepositoryContract;
use App\Athenia\Contracts\Repositories\Organization\OrganizationRepositoryContract;
use App\Athenia\Contracts\Repositories\Payment\LineItemRepositoryContract;
use App\Athenia\Contracts\Repositories\Payment\PaymentMethodRepositoryContract;
use App\Athenia\Contracts\Repositories\Payment\PaymentRepositoryContract;
use App\Athenia\Contracts\Repositories\Subscription\SubscriptionRepositoryContract;
use App\Athenia\Contracts\Repositories\User\UserRepositoryContract;
use App\Athenia\Contracts\Services\AssetImportServiceContract;
use App\Athenia\Contracts\Services\Collection\ItemInEntityCollectionServiceContract;
use App\Athenia\Contracts\Services\DirectoryCopyServiceContract;
use App\Athenia\Contracts\Services\EntitySubscriptionCreationServiceContract;
use App\Athenia\Contracts\Services\ProratingCalculationServiceContract;
use App\Athenia\Contracts\Services\StringHelperServiceContract;
use App\Athenia\Contracts\Services\StripeCustomerServiceContract;
use App\Athenia\Contracts\Services\StripePaymentServiceContract;
use App\Athenia\Contracts\Services\TokenGenerationServiceContract;
use App\Athenia\Contracts\Services\Wiki\ArticleVersionCalculationServiceContract;
use App\Athenia\Services\AssetImportService;
use App\Athenia\Services\Collection\ItemInEntityCollectionService;
use App\Athenia\Services\DirectoryCopyService;
use App\Athenia\Services\EntitySubscriptionCreationService;
use App\Athenia\Services\ProratingCalculationService;
use App\Athenia\Services\StringHelperService;
use App\Athenia\Services\StripeCustomerService;
use App\Athenia\Services\StripePaymentService;
use App\Athenia\Services\TokenGenerationService;
use App\Athenia\Services\Wiki\ArticleVersionCalculationService;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use Laracasts\Generators\GeneratorsServiceProvider;

abstract class BaseServiceProvider extends ServiceProvider
{
    /**
     * @return array
     */
    public function provides(): array
    {
        return array_merge([
            ArticleVersionCalculationServiceContract::class,
            AssetImportServiceContract::class,
            DirectoryCopyServiceContract::class,
            EntitySubscriptionCreationServiceContract::class,
            ItemInEntityCollectionServiceContract::class,
            ProratingCalculationServiceContract::class,
            StringHelperServiceContract::class,
            StripeCustomerServiceContract::class,
            StripePaymentServiceContract::class,
            TokenGenerationServiceContract::class,
        ], $this->appProviders());
    }

    /**
     * All app specific repositories that are provided here
     *
     * @return array
     */
    public abstract function appProviders(): array;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerEnvironmentSpecificProviders();

        $this->app->bind(ArticleVersionCalculationServiceContract::class, fn () =>
            new ArticleVersionCalculationService()
        );
        $this->app->bind(AssetImportServiceContract::class, fn () =>
            new AssetImportService(
                $this->app->make(AssetRepositoryContract::class),
            )
        );
        $this->app->bind(DirectoryCopyServiceContract::class, fn () => 
            new DirectoryCopyService()
        );
        $this->app->bind(EntitySubscriptionCreationServiceContract::class, fn () =>
            new EntitySubscriptionCreationService(
                $this->app->make(ProratingCalculationServiceContract::class),
                $this->app->make(SubscriptionRepositoryContract::class),
                $this->app->make(StripePaymentServiceContract::class),
            )
        );
        $this->app->bind(ItemInEntityCollectionServiceContract::class, fn () =>
            new ItemInEntityCollectionService()
        );
        $this->app->bind(ProratingCalculationServiceContract::class, fn () =>
            new ProratingCalculationService()
        );
        $this->app->bind(StringHelperServiceContract::class, fn () =>
            new StringHelperService()
        );
        $this->app->bind(StripeCustomerServiceContract::class, fn () =>
            new StripeCustomerService(
                $this->app->make(UserRepositoryContract::class),
                $this->app->make(OrganizationRepositoryContract::class),
                $this->app->make(PaymentMethodRepositoryContract::class),
                $this->app->make('stripe')->customers(),
                $this->app->make('stripe')->cards(),
            )
        );
        $this->app->bind(StripePaymentServiceContract::class, function () {
            $stripe = $this->app->make('stripe');
            new StripePaymentService(
                $this->app->make(PaymentRepositoryContract::class),
                $this->app->make(LineItemRepositoryContract::class),
                $this->app->make(Dispatcher::class),
                $stripe->charges(),
                $stripe->refunds(),
            );
        });
        $this->app->bind(TokenGenerationServiceContract::class, fn () =>
            new TokenGenerationService()
        );
        $this->registerApp();
    }

    /**
     * Registers any environment specific rpviders
     */
    public function registerEnvironmentSpecificProviders(): void
    {
        if ($this->app->environment() == 'local') {
            $this->app->register(GeneratorsServiceProvider::class);
            $this->app->register(IdeHelperServiceProvider::class);
        }
    }

    /**
     * Runs any app specific registrations
     *
     * @return void
     */
    public abstract function registerApp(): void;
}