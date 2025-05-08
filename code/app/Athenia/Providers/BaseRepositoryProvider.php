<?php
declare(strict_types=1);

namespace App\Athenia\Providers;

use App\Athenia\Contracts\Repositories\AssetRepositoryContract;
use App\Athenia\Contracts\Repositories\CategoryRepositoryContract;
use App\Athenia\Contracts\Repositories\Collection\CollectionItemRepositoryContract;
use App\Athenia\Contracts\Repositories\Collection\CollectionRepositoryContract;
use App\Athenia\Contracts\Repositories\FeatureRepositoryContract;
use App\Athenia\Contracts\Repositories\Messaging\MessageRepositoryContract;
use App\Athenia\Contracts\Repositories\Messaging\ThreadRepositoryContract;
use App\Athenia\Contracts\Repositories\Organization\OrganizationManagerRepositoryContract;
use App\Athenia\Contracts\Repositories\Organization\OrganizationRepositoryContract;
use App\Athenia\Contracts\Repositories\Payment\LineItemRepositoryContract;
use App\Athenia\Contracts\Repositories\Payment\PaymentMethodRepositoryContract;
use App\Athenia\Contracts\Repositories\Payment\PaymentRepositoryContract;
use App\Athenia\Contracts\Repositories\ResourceRepositoryContract;
use App\Athenia\Contracts\Repositories\RoleRepositoryContract;
use App\Athenia\Contracts\Repositories\Subscription\MembershipPlanRateRepositoryContract;
use App\Athenia\Contracts\Repositories\Subscription\MembershipPlanRepositoryContract;
use App\Athenia\Contracts\Repositories\Subscription\SubscriptionRepositoryContract;
use App\Athenia\Contracts\Repositories\User\ContactRepositoryContract;
use App\Athenia\Contracts\Repositories\User\PasswordTokenRepositoryContract;
use App\Athenia\Contracts\Repositories\User\ProfileImageRepositoryContract;
use App\Athenia\Contracts\Repositories\User\UserRepositoryContract;
use App\Athenia\Contracts\Repositories\Vote\BallotCompletionRepositoryContract;
use App\Athenia\Contracts\Repositories\Vote\BallotItemOptionRepositoryContract;
use App\Athenia\Contracts\Repositories\Vote\BallotItemRepositoryContract;
use App\Athenia\Contracts\Repositories\Vote\BallotRepositoryContract;
use App\Athenia\Contracts\Repositories\Vote\VoteRepositoryContract;
use App\Athenia\Contracts\Repositories\Wiki\ArticleIterationRepositoryContract;
use App\Athenia\Contracts\Repositories\Wiki\ArticleModificationRepositoryContract;
use App\Athenia\Contracts\Repositories\Wiki\ArticleRepositoryContract;
use App\Athenia\Contracts\Repositories\Wiki\ArticleVersionRepositoryContract;
use App\Athenia\Contracts\Services\Asset\AssetConfigurationServiceContract;
use App\Athenia\Contracts\Services\TokenGenerationServiceContract;
use App\Athenia\Repositories\AssetRepository;
use App\Athenia\Repositories\CategoryRepository;
use App\Athenia\Repositories\Collection\CollectionItemRepository;
use App\Athenia\Repositories\Collection\CollectionRepository;
use App\Athenia\Repositories\FeatureRepository;
use App\Athenia\Repositories\Messaging\MessageRepository;
use App\Athenia\Repositories\Messaging\ThreadRepository;
use App\Athenia\Repositories\Organization\OrganizationManagerRepository;
use App\Athenia\Repositories\Organization\OrganizationRepository;
use App\Athenia\Repositories\Payment\LineItemRepository;
use App\Athenia\Repositories\Payment\PaymentMethodRepository;
use App\Athenia\Repositories\Payment\PaymentRepository;
use App\Athenia\Repositories\ResourceRepository;
use App\Athenia\Repositories\RoleRepository;
use App\Athenia\Repositories\Subscription\MembershipPlanRateRepository;
use App\Athenia\Repositories\Subscription\MembershipPlanRepository;
use App\Athenia\Repositories\Subscription\SubscriptionRepository;
use App\Athenia\Repositories\User\ContactRepository;
use App\Athenia\Repositories\User\PasswordTokenRepository;
use App\Athenia\Repositories\User\ProfileImageRepository;
use App\Athenia\Repositories\User\UserRepository;
use App\Athenia\Repositories\Vote\BallotCompletionRepository;
use App\Athenia\Repositories\Vote\BallotItemOptionRepository;
use App\Athenia\Repositories\Vote\BallotItemRepository;
use App\Athenia\Repositories\Vote\BallotRepository;
use App\Athenia\Repositories\Vote\VoteRepository;
use App\Athenia\Repositories\Wiki\ArticleIterationRepository;
use App\Athenia\Repositories\Wiki\ArticleModificationRepository;
use App\Athenia\Repositories\Wiki\ArticleRepository;
use App\Athenia\Repositories\Wiki\ArticleVersionRepository;
use App\Athenia\Services\Asset\AssetConfigurationService;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Collection\Collection;
use App\Models\Collection\CollectionItem;
use App\Models\Feature;
use App\Models\Messaging\Message;
use App\Models\Messaging\Thread;
use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationManager;
use App\Models\Payment\LineItem;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentMethod;
use App\Models\Resource;
use App\Models\Role;
use App\Models\Subscription\MembershipPlan;
use App\Models\Subscription\MembershipPlanRate;
use App\Models\Subscription\Subscription;
use App\Models\User\Contact;
use App\Models\User\PasswordToken;
use App\Models\User\ProfileImage;
use App\Models\User\User;
use App\Models\Vote\Ballot;
use App\Models\Vote\BallotCompletion;
use App\Models\Vote\BallotItem;
use App\Models\Vote\BallotItemOption;
use App\Models\Vote\Vote;
use App\Models\Wiki\Article;
use App\Models\Wiki\ArticleIteration;
use App\Models\Wiki\ArticleModification;
use App\Models\Wiki\ArticleVersion;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

/**
 * Class AtheniaRepositoryProvider
 * @package App\Providers
 */
abstract class BaseRepositoryProvider extends ServiceProvider
{
    /**
     * @return array Holds information on every contract that is provided with this provider
     */
    public final function provides(): array
    {
        return array_merge([
            ArticleRepositoryContract::class,
            ArticleIterationRepositoryContract::class,
            ArticleModificationRepositoryContract::class,
            ArticleVersionRepositoryContract::class,
            AssetRepositoryContract::class,
            BallotRepositoryContract::class,
            BallotCompletionRepositoryContract::class,
            BallotItemRepositoryContract::class,
            BallotItemOptionRepositoryContract::class,
            CategoryRepositoryContract::class,
            CollectionRepositoryContract::class,
            CollectionItemRepositoryContract::class,
            ContactRepositoryContract::class,
            FeatureRepositoryContract::class,
            LineItemRepositoryContract::class,
            MembershipPlanRepositoryContract::class,
            MembershipPlanRateRepositoryContract::class,
            MessageRepositoryContract::class,
            OrganizationRepositoryContract::class,
            OrganizationManagerRepositoryContract::class,
            PasswordTokenRepositoryContract::class,
            PaymentRepositoryContract::class,
            PaymentMethodRepositoryContract::class,
            ProfileImageRepositoryContract::class,
            ResourceRepositoryContract::class,
            RoleRepositoryContract::class,
            SubscriptionRepositoryContract::class,
            ThreadRepositoryContract::class,
            VoteRepositoryContract::class,
            UserRepositoryContract::class,
        ], $this->appProviders());
    }

    /**
     * All app specific repositories that are provided here
     *
     * @return array
     */
    public abstract function appProviders(): array;

    /**
     * Register the repositories.
     *
     * @return void
     */
    public final function register(): void
    {
        Relation::morphMap(array_merge([
            'article' => Article::class,
            'organization' => Organization::class,
            'subscription' => Subscription::class,
            'user' => User::class,
        ], $this->appMorphMaps()));

        $this->app->bind(ArticleRepositoryContract::class, function() {
            return new ArticleRepository(
                new Article(),
                $this->app->make('log'),
            );
        });
        $this->app->bind(ArticleIterationRepositoryContract::class, function() {
            return new ArticleIterationRepository(
                new ArticleIteration(),
                $this->app->make('log'),
            );
        });
        $this->app->bind(ArticleModificationRepositoryContract::class, function() {
            return new ArticleModificationRepository(
                new ArticleModification(),
                $this->app->make('log'),
            );
        });
        $this->app->bind(ArticleVersionRepositoryContract::class, function() {
            return new ArticleVersionRepository(
                new ArticleVersion(),
                $this->app->make('log'),
                $this->app->make(Dispatcher::class),
            );
        });
        $this->app->bind(AssetRepositoryContract::class, function() {
            return new AssetRepository(
                new Asset(),
                $this->app->make('log'),
                $this->app->make('filesystem'),
                $this->app->make(AssetConfigurationServiceContract::class)
            );
        });
        $this->app->bind(BallotRepositoryContract::class, function() {
            return new BallotRepository(
                new Ballot(),
                $this->app->make('log')
            );
        });
        $this->app->bind(BallotCompletionRepositoryContract::class, function() {
            return new BallotCompletionRepository(
                new BallotCompletion(),
                $this->app->make('log')
            );
        });
        $this->app->bind(BallotItemRepositoryContract::class, function() {
            return new BallotItemRepository(
                new BallotItem(),
                $this->app->make('log')
            );
        });
        $this->app->bind(BallotItemOptionRepositoryContract::class, function() {
            return new BallotItemOptionRepository(
                new BallotItemOption(),
                $this->app->make('log')
            );
        });
        $this->app->bind(CategoryRepositoryContract::class, function() {
            return new CategoryRepository(
                new Category(),
                $this->app->make('log')
            );
        });
        $this->app->bind(CollectionRepositoryContract::class, function() {
            return new CollectionRepository(
                new Collection(),
                $this->app->make('log')
            );
        });
        $this->app->bind(CollectionItemRepositoryContract::class, function() {
            return new CollectionItemRepository(
                new CollectionItem(),
                $this->app->make('log')
            );
        });
        $this->app->bind(ContactRepositoryContract::class, function() {
            return new ContactRepository(
                new Contact(),
                $this->app->make('log')
            );
        });
        $this->app->bind(FeatureRepositoryContract::class, function() {
            return new FeatureRepository(
                new Feature(),
                $this->app->make('log')
            );
        });
        $this->app->bind(LineItemRepositoryContract::class, function() {
            return new LineItemRepository(
                new LineItem(),
                $this->app->make('log')
            );
        });
        $this->app->bind(MembershipPlanRepositoryContract::class, function() {
            return new MembershipPlanRepository(
                new MembershipPlan(),
                $this->app->make('log')
            );
        });
        $this->app->bind(MembershipPlanRateRepositoryContract::class, function() {
            return new MembershipPlanRateRepository(
                new MembershipPlanRate(),
                $this->app->make('log')
            );
        });
        $this->app->bind(MessageRepositoryContract::class, function() {
            return new MessageRepository(
                new Message(),
                $this->app->make('log')
            );
        });
        $this->app->bind(OrganizationRepositoryContract::class, function() {
            return new OrganizationRepository(
                new Organization(),
                $this->app->make('log')
            );
        });
        $this->app->bind(OrganizationManagerRepositoryContract::class, function() {
            return new OrganizationManagerRepository(
                new OrganizationManager(),
                $this->app->make('log')
            );
        });
        $this->app->bind(PasswordTokenRepositoryContract::class, function() {
            return new PasswordTokenRepository(
                new PasswordToken(),
                $this->app->make('log'),
                $this->app->make(TokenGenerationServiceContract::class)
            );
        });
        $this->app->bind(PaymentRepositoryContract::class, function() {
            return new PaymentRepository(
                new Payment(),
                $this->app->make('log')
            );
        });
        $this->app->bind(PaymentMethodRepositoryContract::class, function() {
            return new PaymentMethodRepository(
                new PaymentMethod(),
                $this->app->make('log')
            );
        });
        $this->app->bind(ProfileImageRepositoryContract::class, function() {
            return new ProfileImageRepository(
                new ProfileImage(),
                $this->app->make('log'),
                $this->app->make('filesystem')
            );
        });
        $this->app->bind(ResourceRepositoryContract::class, function() {
            return new ResourceRepository(
                new Resource(),
                $this->app->make('log')
            );
        });
        $this->app->bind(RoleRepositoryContract::class, function() {
            return new RoleRepository(
                new Role(),
                $this->app->make('log')
            );
        });
        $this->app->bind(SubscriptionRepositoryContract::class, function() {
            return new SubscriptionRepository(
                new Subscription(),
                $this->app->make('log')
            );
        });
        $this->app->bind(ThreadRepositoryContract::class, function() {
            return new ThreadRepository(
                new Thread(),
                $this->app->make('log')
            );
        });
        $this->app->bind(VoteRepositoryContract::class, function() {
            return new VoteRepository(
                new Vote(),
                $this->app->make('log')
            );
        });
        $this->app->bind(UserRepositoryContract::class, function() {
            return new UserRepository(
                new User(),
                $this->app->make('log'),
                $this->app->make(Hasher::class),
                $this->app->make(Repository::class)
            );
        });
        $this->registerApp();
    }

    /**
     * Gets all morph maps application specific
     *
     * @return array
     */
    public abstract function appMorphMaps(): array;

    /**
     * Runs any app specific registrations
     *
     * @return void
     */
    public abstract function registerApp(): void;
}
