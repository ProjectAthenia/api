<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Observers\Payment;

use App\Athenia\Events\Payment\DefaultPaymentMethodSetEvent;
use App\Athenia\Observers\Payment\PaymentMethodObserver;
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
        $paymentMethod = new PaymentMethod([
            'default' => true,
        ]);

        $this->dispatcher->shouldReceive('dispatch')->once();

        $this->observer->created($paymentMethod);
    }

    public function testUpdated(): void
    {
        $paymentMethod = new PaymentMethod([
            'default' => true,
        ]);

        $this->dispatcher->shouldReceive('dispatch')->once();

        $this->observer->updated($paymentMethod);
    }
}
