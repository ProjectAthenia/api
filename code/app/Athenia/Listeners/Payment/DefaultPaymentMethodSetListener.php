<?php
declare(strict_types=1);

namespace App\Athenia\Listeners\Payment;

use App\Athenia\Contracts\Repositories\Payment\PaymentMethodRepositoryContract;
use App\Athenia\Events\Payment\DefaultPaymentMethodSetEvent;

/**
 * Class DefaultPaymentMethodSetListener
 * @package App\Listeners\Payment
 */
class DefaultPaymentMethodSetListener
{
    /**
     * @var PaymentMethodRepositoryContract
     */
    private PaymentMethodRepositoryContract $paymentMethodRepository;

    /**
     * DefaultPaymentMethodSetListener constructor.
     * @param PaymentMethodRepositoryContract $paymentMethodRepository
     */
    public function __construct(PaymentMethodRepositoryContract $paymentMethodRepository)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    /**
     * @param DefaultPaymentMethodSetEvent $event
     */
    public function handle(DefaultPaymentMethodSetEvent $event)
    {
        $defaultPaymentMethod = $event->getPaymentMethod();

        foreach ($defaultPaymentMethod->owner->paymentMethods as $paymentMethod) {
            if ($paymentMethod->id != $defaultPaymentMethod->id && $paymentMethod->default) {
                $this->paymentMethodRepository->update($paymentMethod, [
                    'default' => false,
                ]);
            }
        }
    }
}
