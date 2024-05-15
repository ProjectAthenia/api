<?php
declare(strict_types=1);

namespace Tests\Athenia\Integration\Policies\Subscription;

use App\Models\User\User;
use App\Policies\Subscription\MembershipPlanRatePolicy;
use Tests\TestCase;

/**
 * Class MembershipPlanRatePolicyTest
 * @package Tests\Athenia\Integration\Policies\Subscription
 */
final class MembershipPlanRatePolicyTest extends TestCase
{
    public function testAll(): void
    {
        $policy = new MembershipPlanRatePolicy();
        $this->assertFalse($policy->all(new User()));
    }
}