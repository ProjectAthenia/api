<?php
declare(strict_types=1);

namespace App\Athenia\Observer\Payment;

use App\Athenia\Events\Payment\DefaultPaymentMethodSetEvent;
use App\Models\Payment\PaymentMethod;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Class PaymentMethodObserver
 * @package App\Observers\Payment
 */
class PaymentMethodObserver
{
    /**
     * @var Dispatcher
     */
    private Dispatcher $dispatcher;

    /**
     * PaymentMethodObserver constructor.
     * @param Dispatcher $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param PaymentMethod $paymentMethod
     */
    public function created(PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->default) {
            $this->dispatcher->dispatch(new DefaultPaymentMethodSetEvent($paymentMethod));
        }
    }

    /**
     * @param PaymentMethod $paymentMethod
     */
    public function updated(PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->default) {
            $this->dispatcher->dispatch(new DefaultPaymentMethodSetEvent($paymentMethod));
        }
    }
}
