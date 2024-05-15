<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Services;

use App\Athenia\Contracts\Models\IsAnEntityContract;
use App\Models\Payment\PaymentMethod;

/**
 * Interface StripeCustomerServiceContract
 * @package App\Contracts\Services
 */
interface StripeCustomerServiceContract
{
    /**
     * Creates a new stripe customer for a user
     *
     * @param IsAnEntityContract $entity
     * @return mixed
     */
    public function createCustomer(IsAnEntityContract $entity);

    /**
     * Retrieves a customer from stripe
     *
     * @param IsAnEntityContract $entity
     * @return mixed
     */
    public function retrieveCustomer(IsAnEntityContract $entity);

    /**
     * Creates a new payment method
     *
     * @param IsAnEntityContract $hasPaymentMethod
     * @param array $paymentData
     * @return mixed
     */
    public function createPaymentMethod(IsAnEntityContract $entity, $paymentData): PaymentMethod;

    /**
     * Interacts with stripe in order to properly delete a user's card
     *
     * @param PaymentMethod $paymentMethod
     * @return mixed
     */
    public function deletePaymentMethod(PaymentMethod $paymentMethod);

    /**
     * Interacts with stripe in order to properly retrieve information on a card
     *
     * @param PaymentMethod $paymentMethod
     * @return mixed
     */
    public function retrievePaymentMethod(PaymentMethod $paymentMethod);
}