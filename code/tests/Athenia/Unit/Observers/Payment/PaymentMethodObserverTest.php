<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Observers\Payment;

use App\Athenia\Observer\Payment\PaymentMethodObserver;
use App\Models\Payment\PaymentMethod;
use Illuminate\Contracts\Events\Dispatcher;
use Tests\CustomMockInterface;
use Tests\TestCase;

/**
 * Class PaymentMethodObserverTest
 * @package Tests\Athenia\Unit\Observers\Payment
 */
final class PaymentMethodObserverTest extends TestCase
{
    /**
     * @var Dispatcher|CustomMockInterface
     */
    private $dispatcher;

    /**
     * @var PaymentMethodObserver
     */
    private PaymentMethodObserver $observer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dispatcher = mock(Dispatcher::class);
        $this->observer = new PaymentMethodObserver($this->dispatcher);
    }

    public function testCreated(): void
    {
        $this->observer->created(new PaymentMethod([
            'default' => false,
        ]));

        $this->dispatcher->shouldReceive('dispatch')->once();

        $this->observer->created(new PaymentMethod([
            'default' => true,
        ]));
    }

    public function testUpdated(): void
    {
        $this->observer->updated(new PaymentMethod([
            'default' => false,
        ]));

        $this->dispatcher->shouldReceive('dispatch')->once();

        $this->observer->updated(new PaymentMethod([
            'default' => true,
        ]));
    }
}
