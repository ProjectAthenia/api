<?php
declare(strict_types=1);

namespace App\Contracts\Services;

use App\Contracts\Models\IsAnEntityContract;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentMethod;
use App\Models\User\User;

/**
 * Interface StripePaymentServiceContract
 * @package App\Contracts\Services
 */
interface StripePaymentServiceContract
{
    /**
     * @param float $amount
     * @param PaymentMethod $paymentMethod
     * @param string $description
     * @param string|null $customerKey
     * @return array
     */
    public function captureCharge(float $amount, PaymentMethod $paymentMethod, string $description, string $customerKey = null);

    /**
     * @param IsAnEntityContract $entity
     * @param PaymentMethod $paymentMethod
     * @param string $description
     * @param array $lineItems
     * @return mixed
     */
    public function createPayment(IsAnEntityContract $entity, PaymentMethod $paymentMethod, string $description, array $lineItems) : Payment;

    /**
     * Reverses a payment, and then triggers an accompanying PaymentReversed Event
     *
     * @param Payment $payment
     * @return mixed
     */
    public function reversePayment(Payment $payment);

    /**
     * Issues a partial refund to the account the
     *
     * @param Payment $payment
     * @param float $amount
     * @return mixed
     */
    public function issuePartialRefund(Payment $payment, float $amount);
}