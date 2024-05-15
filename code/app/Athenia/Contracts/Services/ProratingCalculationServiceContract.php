<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Services;

use App\Models\Subscription\MembershipPlan;
use App\Models\Subscription\Subscription;
use Carbon\Carbon;

/**
 * Interface ProratingCalculationServiceContract
 * @package App\Contracts\Services
 */
interface ProratingCalculationServiceContract
{
    /**
     * Calculates how much is remaining to be charged prorating to change a plan from one to another for a remaining term
     *
     * @param Carbon $toDate
     * @param float $amountPaid
     * @param float $newYearlyAmount
     * @return float
     */
    public function calculateRemainingYearlyCharge(Carbon $toDate, float $amountPaid, float $newYearlyAmount): float;

    /**
     * Calculates how much it will cost to upgrade from the current subscription to the new membership plan
     *
     * @param Subscription $currentSubscription
     * @param MembershipPlan $newMembershipPlan
     * @return float
     */
    public function calculateMembershipUpgradeCharge(Subscription $currentSubscription, MembershipPlan $newMembershipPlan): float;
}
