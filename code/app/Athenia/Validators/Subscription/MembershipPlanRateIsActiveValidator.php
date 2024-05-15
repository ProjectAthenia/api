<?php
declare(strict_types=1);

namespace App\Athenia\Validators\Subscription;

use App\Athenia\Contracts\Repositories\Subscription\MembershipPlanRateRepositoryContract;
use App\Athenia\Validators\BaseValidatorAbstract;
use App\Models\Subscription\MembershipPlanRate;
use Illuminate\Contracts\Validation\Validator;

/**
 * Class MembershipPlanRateIsActiveValidator
 * @package App\Validators\Subscription
 */
class MembershipPlanRateIsActiveValidator extends BaseValidatorAbstract
{
    /**
     * The key this is registered at
     */
    const KEY = 'membership_plan_rate_is_active';

    /**
     * @var MembershipPlanRateRepositoryContract
     */
    private $membershipPlanRateRepository;

    /**
     * MembershipPlanRateIsActiveValidator constructor.
     * @param MembershipPlanRateRepositoryContract $membershipPlanRateRepository
     */
    public function __construct(MembershipPlanRateRepositoryContract $membershipPlanRateRepository)
    {
        $this->membershipPlanRateRepository = $membershipPlanRateRepository;
    }

    /**
     * Responds to 'membership_plan_rate_is_active', and must be attached to the token field
     *
     * @param $attribute
     * @param $value
     * @param array $parameters
     * @param Validator|null $validator
     * @return bool
     */
    public function validate($attribute, $value, $parameters = [], Validator $validator = null)
    {
        $this->ensureValidatorAttribute('membership_plan_rate_id', $attribute);

        try {
            /** @var MembershipPlanRate $membershipPlanRate */
            $membershipPlanRate = $this->membershipPlanRateRepository->findOrFail($value);

            return $membershipPlanRate->active;

        } catch (\Exception $e) {
            return false;
        }
    }
}