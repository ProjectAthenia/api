<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Events\Payment;

use App\Athenia\Events\Payment\DefaultPaymentMethodSetEvent;
use App\Models\Payment\PaymentMethod;
use Tests\TestCase;

/**
 * Class DefaultPaymentMethodSetEventTest
 * @package Tests\Athenia\Unit\Events\Payment
 */
final class DefaultPaymentMethodSetEventTest extends TestCase
{
    public function testGetPaymentMethod(): void
    {
        $paymentMethod = new PaymentMethod();

        $event = new DefaultPaymentMethodSetEvent($paymentMethod);

        $this->assertEquals($paymentMethod, $event->getPaymentMethod());
    }
}
