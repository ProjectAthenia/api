<?php
declare(strict_types=1);

namespace App\Athenia\Events\Payment;

use App\Models\Payment\PaymentMethod;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class DefaultPaymentMethodSetEvent
 * @package App\Events\Payment
 */
class DefaultPaymentMethodSetEvent implements ShouldQueue
{
    use Queueable;

    /**
     * @var PaymentMethod
     */
    private PaymentMethod $paymentMethod;

    /**
     * DefaultPaymentMethodSetEvent constructor.
     * @param PaymentMethod $paymentMethod
     */
    public function __construct(PaymentMethod $paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * @return PaymentMethod
     */
    public function getPaymentMethod(): PaymentMethod
    {
        return $this->paymentMethod;
    }
}
