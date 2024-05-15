<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Providers;

use App\Providers\EventServiceProvider;
use ReflectionClass;
use Tests\TestCase;
use Tests\Traits\ReflectionHelpers;

/**
 * Class EventServiceProviderTest
 * @package Tests\Athenia\Unit\Providers
 */
final class EventServiceProviderTest extends TestCase
{
    use ReflectionHelpers;

    public function testAllNonAbstractEventsAreRegistered(): void
    {
        $foundEvents = $this->getObjectsInNamespace('App\Events');

        $provider = new EventServiceProvider($this->app);

        $registeredEvents = array_keys($provider->listens());

        $nonAbstractEvents = [];
        foreach ($foundEvents as $event) {
            $reflection = new ReflectionClass($event);

            if (!$reflection->isAbstract()) {
                $nonAbstractEvents[] = $event;
            }
        }

        $unregisteredEvents = array_diff($nonAbstractEvents, $registeredEvents);

        if (count($unregisteredEvents)) {
            $this->fail('Not all events have been registered. Make sure the following events have listeners attached to it - ' . implode($unregisteredEvents));
        }
    }

    public function testAllNonAbstractListenersAreRegistered(): void
    {
        $foundListeners = $this->getObjectsInNamespace('App\Listeners');

        $provider = new EventServiceProvider($this->app);

        $registeredListeners = [];
        foreach (array_values($provider->listens()) as $eventData) {
            $registeredListeners = array_merge($registeredListeners, $eventData);
        }

        $nonAbstractListeners = [];
        foreach ($foundListeners as $listener) {
            $reflection = new ReflectionClass($listener);

            if (!$reflection->isAbstract()) {
                $nonAbstractListeners[] = $listener;
            }
        }

        $unregisteredListeners = array_diff($nonAbstractListeners, $registeredListeners);

        if (count($unregisteredListeners)) {
            $this->fail('Not all listeners have been registered. Make sure the following listeners are listening to an event - ' . implode($unregisteredListeners));
        }
    }
}