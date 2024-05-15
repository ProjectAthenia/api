<?php
declare(strict_types=1);

namespace Tests\Athenia\Integration\Policies\Subscription;

use App\Models\Subscription\MembershipPlan;
use App\Models\User\User;
use App\Policies\Subscription\MembershipPlanPolicy;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class MembershipPlanPolicyTest
 * @package Tests\Athenia\Integration\Policies\Subscription
 */
final class MembershipPlanPolicyTest extends TestCase
{
    use DatabaseSetupTrait;

    public function testAll(): void
    {
        $policy = new MembershipPlanPolicy();

        $this->assertTrue($policy->all(new User()));
    }

    public function testView(): void
    {
        $policy = new MembershipPlanPolicy();

        $this->assertTrue($policy->view(new User(), new MembershipPlan()));
    }

    public function testCreate(): void
    {
        $policy = new MembershipPlanPolicy();

        $this->assertFalse($policy->create(new User()));
    }

    public function testUpdate(): void
    {
        $policy = new MembershipPlanPolicy();

        $this->assertFalse($policy->update(new User(), new MembershipPlan()));
    }

    public function testDelete(): void
    {
        $policy = new MembershipPlanPolicy();

        $this->assertFalse($policy->delete(new User(), new MembershipPlan()));
    }
}