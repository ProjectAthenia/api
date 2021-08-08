<?php
declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Repositories\AssetRepositoryContract;
use App\Contracts\Repositories\FeatureRepositoryContract;
use App\Contracts\Repositories\Organization\OrganizationManagerRepositoryContract;
use App\Contracts\Repositories\Organization\OrganizationRepositoryContract;
use App\Contracts\Repositories\Payment\LineItemRepositoryContract;
use App\Contracts\Repositories\Payment\PaymentMethodRepositoryContract;
use App\Contracts\Repositories\Payment\PaymentRepositoryContract;
use App\Contracts\Repositories\ResourceRepositoryContract;
use App\Contracts\Repositories\RoleRepositoryContract;
use App\Contracts\Repositories\Subscription\MembershipPlanRateRepositoryContract;
use App\Contracts\Repositories\Subscription\MembershipPlanRepositoryContract;
use App\Contracts\Repositories\Subscription\SubscriptionRepositoryContract;
use App\Contracts\Repositories\User\ContactRepositoryContract;
use App\Contracts\Repositories\User\MessageRepositoryContract;
use App\Contracts\Repositories\User\PasswordTokenRepositoryContract;
use App\Contracts\Repositories\User\ProfileImageRepositoryContract;
use App\Contracts\Repositories\User\ThreadRepositoryContract;
use App\Contracts\Repositories\Vote\BallotCompletionRepositoryContract;
use App\Contracts\Repositories\Vote\BallotItemOptionRepositoryContract;
use App\Contracts\Repositories\Vote\BallotRepositoryContract;
use App\Contracts\Repositories\Vote\BallotItemRepositoryContract;
use App\Contracts\Repositories\Vote\VoteRepositoryContract;
use App\Contracts\Repositories\Wiki\ArticleRepositoryContract;
use App\Contracts\Repositories\Wiki\ArticleVersionRepositoryContract;
use App\Contracts\Repositories\Wiki\IterationRepositoryContract;
use App\Contracts\Services\TokenGenerationServiceContract;
use App\Models\Asset;
use App\Models\Feature;
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
use App\Models\User\Message;
use App\Models\User\PasswordToken;
use App\Models\User\ProfileImage;
use App\Models\User\Thread;
use App\Models\Vote\Ballot;
use App\Models\Vote\BallotCompletion;
use App\Models\Vote\BallotItem;
use App\Models\Vote\BallotItemOption;
use App\Models\Vote\Vote;
use App\Models\Wiki\Article;
use App\Models\Wiki\ArticleVersion;
use App\Models\Wiki\ArticleIteration;
use App\Repositories\AssetRepository;
use App\Repositories\FeatureRepository;
use App\Repositories\Organization\OrganizationManagerRepository;
use App\Repositories\Organization\OrganizationRepository;
use App\Repositories\Payment\LineItemRepository;
use App\Repositories\Payment\PaymentMethodRepository;
use App\Repositories\Payment\PaymentRepository;
use App\Repositories\ResourceRepository;
use App\Repositories\RoleRepository;
use App\Repositories\Subscription\MembershipPlanRateRepository;
use App\Repositories\Subscription\MembershipPlanRepository;
use App\Repositories\Subscription\SubscriptionRepository;
use App\Repositories\User\ContactRepository;
use App\Repositories\User\MessageRepository;
use App\Repositories\User\PasswordTokenRepository;
use App\Repositories\User\ProfileImageRepository;
use App\Repositories\User\ThreadRepository;
use App\Repositories\Vote\BallotCompletionRepository;
use App\Repositories\Vote\BallotItemOptionRepository;
use App\Repositories\Vote\BallotRepository;
use App\Repositories\Vote\BallotItemRepository;
use App\Repositories\Vote\VoteRepository;
use App\Repositories\Wiki\ArticleRepository;
use App\Repositories\Wiki\ArticleVersionRepository;
use App\Repositories\Wiki\IterationRepository;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use App\Contracts\Repositories\User\UserRepositoryContract;
use App\Models\User\User;
use App\Repositories\User\UserRepository;

/**
 * Class AtheniaRepositoryProvider
 * @package App\Providers
 */
abstract class AtheniaRepositoryProvider extends ServiceProvider
{
    /**
     * @return array Holds information on every contract that is provided with this provider
     */
    public final function provides()
    {
        return array_merge([
            ArticleRepositoryContract::class,
            ArticleVersionRepositoryContract::class,
            AssetRepositoryContract::class,
            BallotRepositoryContract::class,
            BallotCompletionRepositoryContract::class,
            BallotItemRepositoryContract::class,
            BallotItemOptionRepositoryContract::class,
            ContactRepositoryContract::class,
            FeatureRepositoryContract::class,
            IterationRepositoryContract::class,
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
    public final function register()
    {
        Relation::morphMap(array_merge([
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
                $this->app->make('config')->get('app.asset_url'),
                "assets"
            );
        });
        $this->app->bind(BallotRepositoryContract::class, function () {
            return new BallotRepository(
                new Ballot(),
                $this->app->make('log'),
                $this->app->make(BallotItemRepositoryContract::class),
            );
        });
        $this->app->bind(BallotCompletionRepositoryContract::class, function () {
            return new BallotCompletionRepository(
                new BallotCompletion(),
                $this->app->make('log'),
                $this->app->make(VoteRepositoryContract::class),
            );
        });
        $this->app->bind(BallotItemOptionRepositoryContract::class, function () {
            return new BallotItemOptionRepository(
                new BallotItemOption(),
                $this->app->make('log'),
            );
        });
        $this->app->bind(BallotItemRepositoryContract::class, function () {
            return new BallotItemRepository(
                new BallotItem(),
                $this->app->make('log'),
                $this->app->make(BallotItemOptionRepositoryContract::class),
            );
        });
        $this->app->bind(ContactRepositoryContract::class, function () {
            return new ContactRepository(
                new Contact(),
                $this->app->make('log'),
            );
        });
        $this->app->bind(FeatureRepositoryContract::class, function() {
            return new FeatureRepository(new Feature(), $this->app->make('log'));
        });
        $this->app->bind(IterationRepositoryContract::class, function() {
            return new IterationRepository(new ArticleIteration(), $this->app->make('log'));
        });
        $this->app->bind(LineItemRepositoryContract::class, function () {
            return new LineItemRepository(
                new LineItem(),
                $this->app->make('log'),
            );
        });
        $this->app->bind(MembershipPlanRepositoryContract::class, function() {
            return new MembershipPlanRepository(
                new MembershipPlan(),
                $this->app->make('log'),
                $this->app->make(MembershipPlanRateRepositoryContract::class),
            );
        });
        $this->app->bind(MembershipPlanRateRepositoryContract::class, function() {
            return new MembershipPlanRateRepository(
                new MembershipPlanRate(),
                $this->app->make('log'),
            );
        });
        $this->app->bind(MessageRepositoryContract::class, function() {
            return new MessageRepository(
                new Message(),
                $this->app->make('log'),
                $this->app->make(UserRepositoryContract::class),
            );
        });
        $this->app->bind(OrganizationRepositoryContract::class, function () {
            return new OrganizationRepository(new Organization(), $this->app->make('log'));
        });
        $this->app->bind(OrganizationManagerRepositoryContract::class, function () {
            return new OrganizationManagerRepository(new OrganizationManager(), $this->app->make('log'));
        });
        $this->app->bind(PasswordTokenRepositoryContract::class, function() {
            return new PasswordTokenRepository(
                new PasswordToken(),
                $this->app->make('log'),
                $this->app->make(Dispatcher::class),
                $this->app->make(TokenGenerationServiceContract::class),
            );
        });
        $this->app->bind(PaymentRepositoryContract::class, function() {
            return new PaymentRepository(
                new Payment(),
                $this->app->make('log'),
                $this->app->make(LineItemRepositoryContract::class),
            );
        });
        $this->app->bind(PaymentMethodRepositoryContract::class, function() {
            return new PaymentMethodRepository(
                new PaymentMethod(),
                $this->app->make('log'),
            );
        });
        $this->app->bind(ProfileImageRepositoryContract::class, function() {
            return new ProfileImageRepository(
                new ProfileImage(),
                $this->app->make('log'),
                $this->app->make('filesystem'),
                $this->app->make('config')->get('app.asset_url'),
                "profile_images"
            );
        });
        $this->app->bind(ResourceRepositoryContract::class, function() {
            return new ResourceRepository(
                new Resource(),
                $this->app->make('log'),
            );
        });
        $this->app->bind(RoleRepositoryContract::class, function() {
            return new RoleRepository(
                new Role(),
                $this->app->make('log'),
            );
        });
        $this->app->bind(SubscriptionRepositoryContract::class, function() {
            return new SubscriptionRepository(
                new Subscription(),
                $this->app->make('log'),
                $this->app->make(MembershipPlanRateRepositoryContract::class),
            );
        });
        $this->app->bind(ThreadRepositoryContract::class, function() {
            return new ThreadRepository(
                new Thread(),
                $this->app->make('log'),
            );
        });
        $this->app->bind(UserRepositoryContract::class, function() {
            return new UserRepository(
                new User(),
                $this->app->make('log'),
                $this->app->make(Hasher::class),
                $this->app->make(Repository::class),
            );
        });
        $this->app->bind(VoteRepositoryContract::class, function () {
            return new VoteRepository(
                new Vote(),
                $this->app->make('log'),
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
     * @return mixed
     */
    public abstract function registerApp();
}
