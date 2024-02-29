<?php
declare(strict_types=1);

namespace Tests\Unit\Events\Payment;

use App\Events\Payment\DefaultPaymentMethodSetEvent;
use App\Models\Payment\PaymentMethod;
use Tests\TestCase;

/**
 * Class DefaultPaymentMethodSetEventTest
 * @package Tests\Unit\Events\Payment
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
