<?php
declare(strict_types=1);

namespace App\Providers\AtheniaProviders;

use App\Contracts\Repositories\AssetRepositoryContract;
use App\Contracts\Repositories\Organization\OrganizationRepositoryContract;
use App\Contracts\Repositories\Payment\LineItemRepositoryContract;
use App\Contracts\Repositories\Payment\PaymentMethodRepositoryContract;
use App\Contracts\Repositories\Payment\PaymentRepositoryContract;
use App\Contracts\Repositories\Subscription\SubscriptionRepositoryContract;
use App\Contracts\Repositories\User\UserRepositoryContract;
use App\Contracts\Services\AssetImportServiceContract;
use App\Contracts\Services\Collection\ItemInEntityCollectionServiceContract;
use App\Contracts\Services\DirectoryCopyServiceContract;
use App\Contracts\Services\EntitySubscriptionCreationServiceContract;
use App\Contracts\Services\ProratingCalculationServiceContract;
use App\Contracts\Services\StringHelperServiceContract;
use App\Contracts\Services\StripeCustomerServiceContract;
use App\Contracts\Services\StripePaymentServiceContract;
use App\Contracts\Services\TokenGenerationServiceContract;
use App\Contracts\Services\Wiki\ArticleVersionCalculationServiceContract;
use App\Services\AssetImportService;
use App\Services\Collection\ItemInEntityCollectionService;
use App\Services\DirectoryCopyService;
use App\Services\EntitySubscriptionCreationService;
use App\Services\ProratingCalculationService;
use App\Services\StringHelperService;
use App\Services\StripeCustomerService;
use App\Services\StripePaymentService;
use App\Services\TokenGenerationService;
use App\Services\Wiki\ArticleVersionCalculationService;
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