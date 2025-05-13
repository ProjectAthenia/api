<?php
declare(strict_types=1);

namespace App\Athenia\Providers;

use App\Athenia\Events\Article\ArticleVersionCreatedEvent;
use App\Athenia\Events\Messaging\MessageCreatedEvent;
use App\Athenia\Events\Messaging\MessageSentEvent;
use App\Athenia\Events\Organization\OrganizationManagerCreatedEvent;
use App\Athenia\Events\Payment\DefaultPaymentMethodSetEvent;
use App\Athenia\Events\Payment\PaymentReversedEvent;
use App\Athenia\Events\User\Contact\ContactCreatedEvent;
use App\Athenia\Events\User\ForgotPasswordEvent;
use App\Athenia\Events\User\SignUpEvent;
use App\Athenia\Events\User\UserMergeEvent;
use App\Athenia\Events\Vote\VoteCreatedEvent;
use App\Athenia\Events\Statistics\StatisticUpdatedEvent;
use App\Athenia\Events\Statistics\StatisticCreatedEvent;
use App\Athenia\Events\Statistics\StatisticDeletedEvent;
use App\Athenia\Listeners\Article\ArticleVersionCreatedListener;
use App\Athenia\Listeners\Messaging\MessageCreatedListener;
use App\Athenia\Listeners\Messaging\MessageSentListener;
use App\Athenia\Listeners\Payment\DefaultPaymentMethodSetListener;
use App\Athenia\Listeners\User\ForgotPasswordListener;
use App\Athenia\Listeners\User\UserMerge\UserBallotCompletionsMergeListener;
use App\Athenia\Listeners\User\UserMerge\UserCreatedArticlesMergeListener;
use App\Athenia\Listeners\User\UserMerge\UserCreatedIterationsMergeListener;
use App\Athenia\Listeners\User\UserMerge\UserMessagesMergeListener;
use App\Athenia\Listeners\User\UserMerge\UserPropertiesMergeListener;
use App\Athenia\Listeners\User\UserMerge\UserSubscriptionsMergeListener;
use App\Athenia\Observers\IndexableModelObserver;
use App\Athenia\Observers\Payment\PaymentMethodObserver;
use App\Listeners\Organization\OrganizationManagerCreatedListener;
use App\Listeners\User\Contact\ContactCreatedListener;
use App\Listeners\User\SignUpListener;
use App\Listeners\Vote\VoteCreatedListener;
use App\Athenia\Listeners\Statistics\StatisticUpdatedListener;
use App\Athenia\Listeners\Statistics\StatisticCreatedListener;
use App\Athenia\Listeners\Statistics\StatisticDeletedListener;
use App\Models\Payment\PaymentMethod;
use App\Models\User\User;
use App\Models\Wiki\Article;
use App\Models\Collection\Collection;
use App\Athenia\Observers\AggregatedModelObserver;
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
            StatisticUpdatedEvent::class => [
                StatisticUpdatedListener::class,
            ],
            StatisticCreatedEvent::class => [
                StatisticCreatedListener::class,
            ],
            StatisticDeletedEvent::class => [
                StatisticDeletedListener::class,
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
        Collection::observe(AggregatedModelObserver::class);

        $this->registerObservers();
    }

    /**
     * Registers any application specific observers
     *
     * @return void
     */
    public abstract function registerObservers(): void;
}
