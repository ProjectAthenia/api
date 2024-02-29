<?php
declare(strict_types=1);

namespace Tests\Unit\Events\Payment;

use App\Events\Payment\PaymentReversedEvent;
use App\Models\Payment\Payment;
use Tests\TestCase;

/**
 * Class PaymentReversedEventTest
 * @package Tests\Unit\Events\Payment
 */
final class PaymentReversedEventTest extends TestCase
{
    public function testGetPayment(): void
    {
        $payment = new Payment();

        $event = new PaymentReversedEvent($payment);

        $this->assertEquals($payment, $event->getPayment());
    }
}