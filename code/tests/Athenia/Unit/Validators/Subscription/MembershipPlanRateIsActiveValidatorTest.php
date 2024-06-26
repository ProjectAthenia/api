<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Validators\Subscription;

use App\Athenia\Contracts\Repositories\Subscription\MembershipPlanRateRepositoryContract;
use App\Athenia\Validators\Subscription\MembershipPlanRateIsActiveValidator;
use App\Models\Subscription\MembershipPlanRate;
use Cartalyst\Stripe\Exception\NotFoundException;
use Tests\TestCase;

/**
 * Class MembershipPlanRateIsActiveValidatorTest
 * @package Tests\Athenia\Unit\Validators\Subscription
 */
final class MembershipPlanRateIsActiveValidatorTest extends TestCase
{
    public function testValidateFailsWithNonExistingRate(): void
    {
        $repository = mock(MembershipPlanRateRepositoryContract::class);
        $validator = new MembershipPlanRateIsActiveValidator($repository);

        $repository->shouldReceive('findOrFail')->andThrow(new NotFoundException());

        $this->assertFalse($validator->validate('membership_plan_rate_id', 214));
    }

    public function testValidateFailsMembershipPlanRateNotActive(): void
    {
        $repository = mock(MembershipPlanRateRepositoryContract::class);
        $validator = new MembershipPlanRateIsActiveValidator($repository);

        $membershipPlanRate = new MembershipPlanRate([
            'active' => false,
        ]);
        $repository->shouldReceive('findOrFail')->andReturn($membershipPlanRate);

        $this->assertFalse($validator->validate('membership_plan_rate_id', 214));
    }

    public function testValidateSuccess(): void
    {
        $repository = mock(MembershipPlanRateRepositoryContract::class);
        $validator = new MembershipPlanRateIsActiveValidator($repository);

        $membershipPlanRate = new MembershipPlanRate([
            'active' => true,
        ]);
        $repository->shouldReceive('findOrFail')->andReturn($membershipPlanRate);

        $this->assertTrue($validator->validate('membership_plan_rate_id', 214));
    }
}