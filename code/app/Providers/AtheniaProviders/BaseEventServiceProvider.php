<?php
declare(strict_types=1);

namespace App\Providers\AtheniaProviders;

use App\Events\Article\ArticleVersionCreatedEvent;
use App\Events\Messaging\MessageCreatedEvent;
use App\Events\Messaging\MessageSentEvent;
use App\Events\Organization\OrganizationManagerCreatedEvent;
use App\Events\Payment\DefaultPaymentMethodSetEvent;
use App\Events\Payment\PaymentReversedEvent;
use App\Events\User\Contact\ContactCreatedEvent;
use App\Events\User\ForgotPasswordEvent;
use App\Events\User\SignUpEvent;
use App\Events\User\UserMergeEvent;
use App\Events\Vote\VoteCreatedEvent;
use App\Listeners\Article\ArticleVersionCreatedListener;
use App\Listeners\Messaging\MessageCreatedListener;
use App\Listeners\Messaging\MessageSentListener;
use App\Listeners\Organization\OrganizationManagerCreatedListener;
use App\Listeners\Payment\DefaultPaymentMethodSetListener;
use App\Listeners\User\Contact\ContactCreatedListener;
use App\Listeners\User\ForgotPasswordListener;
use App\Listeners\User\SignUpListener;
use App\Listeners\User\UserMerge\UserBallotCompletionsMergeListener;
use App\Listeners\User\UserMerge\UserCreatedArticlesMergeListener;
use App\Listeners\User\UserMerge\UserCreatedIterationsMergeListener;
use App\Listeners\User\UserMerge\UserMessagesMergeListener;
use App\Listeners\User\UserMerge\UserPropertiesMergeListener;
use App\Listeners\User\UserMerge\UserSubscriptionsMergeListener;
use App\Listeners\Vote\VoteCreatedListener;
use App\Models\Payment\PaymentMethod;
use App\Models\User\User;
use App\Models\Wiki\Article;
use App\Observers\IndexableModelObserver;
use App\Observers\Payment\PaymentMethodObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider
 * @package App\Providers
 */
abstract class BaseEventServiceProvider extends ServiceProvider
{
    /**
     * Gets all listeners and events for the whole app
     *
     * @return array
     */
    public function listens(): array
    {
        return array_merge([
            ArticleVersionCreatedEvent::class => [
                ArticleVersionCreatedListener::class,
            ],
            ContactCreatedEvent::class => [
                ContactCreatedListener::class,
            ],
            DefaultPaymentMethodSetEvent::class => [
                DefaultPaymentMethodSetListener::class,
            ],
            ForgotPasswordEvent::class => [
                ForgotPasswordListener::class,
            ],
            MessageCreatedEvent::class => [
                MessageCreatedListener::class,
            ],
            MessageSentEvent::class => [
                MessageSentListener::class,
            ],
            OrganizationManagerCreatedEvent::class => [
                OrganizationManagerCreatedListener::class,
            ],
            PaymentReversedEvent::class => [

            ],
            SignUpEvent::class => [
                SignUpListener::class,
            ],
            UserMergeEvent::class => array_merge([
                UserBallotCompletionsMergeListener::class,
                UserCreatedArticlesMergeListener::class,
                UserCreatedIterationsMergeListener::class,
                UserMessagesMergeListener::class,
                UserPropertiesMergeListener::class,
                UserSubscriptionsMergeListener::class,
            ], $this->getAppUserMergeListeners()),
            VoteCreatedEvent::class => [
                VoteCreatedListener::class,
            ],
        ], $this->getAppListenerMapping());
    }

    /**
     * Gets all application level event and mappings
     *
     * @return array
     */
    public abstract function getAppListenerMapping(): array;

    /**
     * Gets all application specific listeners for when a user is merged within the Athenia pipeline
     *
     * @return array
     */
    public abstract function getAppUserMergeListeners(): array;

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Article::observe(IndexableModelObserver::class);
        User::observe(IndexableModelObserver::class);
        PaymentMethod::observe(PaymentMethodObserver::class);

        $this->registerObservers();
    }

    /**
     * Registers any application specific observers
     *
     * @return void
     */
    public abstract function registerObservers(): void;
}
