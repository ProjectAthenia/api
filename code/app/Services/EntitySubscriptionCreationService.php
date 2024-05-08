<?php
declare(strict_types=1);

namespace App\Services;

use App\Contracts\Models\IsAnEntityContract;
use App\Contracts\Repositories\Subscription\SubscriptionRepositoryContract;
use App\Contracts\Services\EntitySubscriptionCreationServiceContract;
use App\Contracts\Services\ProratingCalculationServiceContract;
use App\Contracts\Services\StripePaymentServiceContract;
use App\Models\Subscription\Subscription;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

/**
 * Class EntitySubscriptionCreationService
 * @package App\Services
 */
class EntitySubscriptionCreationService implements EntitySubscriptionCreationServiceContract
{
    /**
     * @var ProratingCalculationServiceContract
     */
    private ProratingCalculationServiceContract $proratingCalculationService;

    /**
     * @var SubscriptionRepositoryContract
     */
    private SubscriptionRepositoryContract $subscriptionRepository;

    /**
     * @var StripePaymentServiceContract
     */
    private StripePaymentServiceContract $stripePaymentService;

    /**
     * EntitySubscriptionCreationService constructor.
     * @param ProratingCalculationServiceContract $proratingCalculationService
     * @param SubscriptionRepositoryContract $subscriptionRepository
     * @param StripePaymentServiceContract $stripePaymentService
     */
    public function __construct(ProratingCalculationServiceContract $proratingCalculationService,
                                SubscriptionRepositoryContract $subscriptionRepository,
                                StripePaymentServiceContract $stripePaymentService)
    {
        $this->proratingCalculationService = $proratingCalculationService;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->stripePaymentService = $stripePaymentService;
    }

    /**
     * Creates a subscription for an entity while replacing any current one that may exist for an entity
     *
     * @param IsAnEntityContract $entity
     * @param array $data
     * @return Subscription
     */
    public function createSubscription(IsAnEntityContract $entity, array $data): Subscription
    {
        $currentSubscription = $entity->currentSubscription(Carbon::now()->endOfDay());

        $data['subscriber_id'] = $entity->id;
        $data['subscriber_type'] = $entity->morphRelationName();

        $model = null;
        try {

            /** @var Subscription $model */
            $model = $this->subscriptionRepository->create($data);
            if ($currentSubscription && !$model->isLifetime()) {
                $data['expires_at'] = $currentSubscription->expires_at;
                $data['recurring'] = $currentSubscription->recurring;
                $amount = $this->proratingCalculationService->calculateMembershipUpgradeCharge(
                    $currentSubscription,
                    $model->membershipPlanRate->membershipPlan
                );
            } else {
                $amount = (float)$model->membershipPlanRate->cost;
            }

            if (!$model->is_trial) {
                $this->stripePaymentService->createPayment($entity, $model->paymentMethod,
                    'Subscription Payment for ' . $model->membershipPlanRate->membershipPlan->name, [
                        [
                            'item_id' => $model->id,
                            'item_type' => 'subscription',
                            'amount' => $amount,
                        ]
                    ]);
            }

            if ($currentSubscription) {
                $this->subscriptionRepository->update($currentSubscription, [
                    'canceled_at' => Carbon::now(),
                ]);
            }

        } catch (\Exception $e) {
            if ($model) {
                $this->subscriptionRepository->delete($model);
            }
            throw new ServiceUnavailableHttpException(5, 'Unable to accept payments right now');
        }

        return $model;
    }
}
