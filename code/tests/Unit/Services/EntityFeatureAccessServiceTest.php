<?php
declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Contracts\Repositories\Subscription\MembershipPlanRepositoryContract;
use App\Models\Feature;
use App\Models\Subscription\MembershipPlan;
use App\Models\Subscription\MembershipPlanRate;
use App\Models\Subscription\Subscription;
use App\Models\User\User;
use App\Services\EntityFeatureAccessService;
use Carbon\Carbon;
use Tests\CustomMockInterface;
use Tests\TestCase;

/**
 * Class EntityFeatureAccessServiceTest
 * @package Tests\Unit\Services
 */
final class EntityFeatureAccessServiceTest extends TestCase
{
    /**
     * @var MembershipPlanRepositoryContract|CustomMockInterface
     */
    private $membershipPlanRepository;

    /**
     * @var EntityFeatureAccessService
     */
    private EntityFeatureAccessService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->membershipPlanRepository = mock(MembershipPlanRepositoryContract::class);
        $this->service = new EntityFeatureAccessService($this->membershipPlanRepository);
    }

    public function testCanAccessReturnsFalseWithoutDefaultMembershipPlan(): void
    {
        $user = new User([
            'subscriptions' => collect([]),
        ]);

        $this->membershipPlanRepository
            ->shouldReceive('findDefaultMembershipPlanForEntity')
            ->once()->with('user')->andReturnNull();

        $this->assertFalse($this->service->canAccess($user, 21));
    }

    public function testCanAccessReturnsFalseWhenDefaultMembershipPlanDoesNotContainFeature(): void
    {
        $feature = new Feature();
        $feature->id = 12;

        $membershipPlan = new MembershipPlan([
            'features' => collect([
                $feature,
            ]),
        ]);

        $user = new User([
            'subscriptions' => collect([]),
        ]);

        $this->membershipPlanRepository
            ->shouldReceive('findDefaultMembershipPlanForEntity')
            ->once()->with('user')->andReturn($membershipPlan);

        $this->assertFalse($this->service->canAccess($user, 21));
    }

    public function testCanAccessReturnsTrueWhenDefaultMembershipPlanDoesContainsFeature(): void
    {
        $feature = new Feature();
        $feature->id = 21;

        $membershipPlan = new MembershipPlan([
            'features' => collect([
                $feature,
            ]),
        ]);

        $user = new User([
            'subscriptions' => collect([]),
        ]);

        $this->membershipPlanRepository
            ->shouldReceive('findDefaultMembershipPlanForEntity')
            ->once()->with('user')->andReturn($membershipPlan);

        $this->assertTrue($this->service->canAccess($user, 21));
    }

    public function testCanAccessReturnsFalseWhenEntityMembershipPlanDoesNotContainFeature(): void
    {
        $feature = new Feature();
        $feature->id = 12;

        $membershipPlan = new MembershipPlan([
            'features' => collect([
                $feature,
            ]),
        ]);

        $user = new User([
            'subscriptions' => collect([
                new Subscription([
                    'expires_at' => Carbon::now()->addYear(),
                    'membershipPlanRate' => new MembershipPlanRate([
                        'membershipPlan' => $membershipPlan,
                    ])
                ])
            ]),
        ]);

        $this->assertFalse($this->service->canAccess($user, 21));
    }

    public function testCanAccessReturnsTrueWhenEnityMembershipPlanDoesContainsFeature(): void
    {
        $feature = new Feature();
        $feature->id = 21;

        $membershipPlan = new MembershipPlan([
            'features' => collect([
                $feature,
            ]),
        ]);

        $user = new User([
            'subscriptions' => collect([
                new Subscription([
                    'expires_at' => Carbon::now()->addYear(),
                    'membershipPlanRate' => new MembershipPlanRate([
                        'membershipPlan' => $membershipPlan,
                    ])
                ])
            ]),
        ]);

        $this->assertTrue($this->service->canAccess($user, 21));
    }
}
