<?php
declare(strict_types=1);

namespace App\Athenia\Events\Payment;

use App\Models\Payment\Payment;

/**
 * Class PaymentReversedEvent
 * @package App\Events\Payment
 */
class PaymentReversedEvent
{
    /**
     * @var Payment
     */
    private $payment;

    /**
     * PaymentReversedEvent constructor.
     * @param Payment $payment
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * @return Payment
     */
    public function getPayment(): Payment
    {
        return $this->payment;
    }
}