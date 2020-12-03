<?php
declare(strict_types=1);

namespace App\Services;

use App\Contracts\Services\ProratingCalculationServiceContract;
use App\Models\Subscription\MembershipPlan;
use App\Models\Subscription\Subscription;
use Carbon\Carbon;

/**
 * Class ProratingCalculationService
 * @package App\Services
 */
class ProratingCalculationService implements ProratingCalculationServiceContract
{
    /**
     * Calculates how much is remaining to be charged prorating to change a plan from one to another for a remaining term
     *
     * @param Carbon $toDate
     * @param float $amountPaid
     * @param float $newYearlyAmount
     * @return float
     */
    public function calculateRemainingYearlyCharge(Carbon $toDate, float $amountPaid, float $newYearlyAmount): float
    {
        $daysRemaining = Carbon::now()->diffInDays($toDate, false);
        $dailyCostDiff = ($newYearlyAmount - $amountPaid) / 365;

        return $daysRemaining > 0 && $dailyCostDiff > 0 ? round($daysRemaining * $dailyCostDiff, 2) : 0;
    }

    /**
     * Calculates how much it will cost to upgrade from the current subscription to the new membership plan
     *
     * @param Subscription $currentSubscription
     * @param MembershipPlan $newMembershipPlan
     * @return float
     */
    public function calculateMembershipUpgradeCharge(Subscription $currentSubscription, MembershipPlan $newMembershipPlan): float
    {
        $currentSubscriptionCost = $currentSubscription->membershipPlanRate->cost;
        $newCost = $newMembershipPlan->current_cost ?? 0;

        if ($newCost == 0) {
            return 0;
        }

        if ($newMembershipPlan->duration == MembershipPlan::DURATION_LIFETIME) {
            $oldLength = Carbon::now()->diffInDays($currentSubscription->subscribed_at, true);
            return $oldLength <= 90 ?
                $newCost - $currentSubscriptionCost : $newCost;
        }

        return $currentSubscription->expires_at ?
            $this->calculateRemainingYearlyCharge($currentSubscription->expires_at, $currentSubscriptionCost, $newCost) : $newCost;
    }
}
