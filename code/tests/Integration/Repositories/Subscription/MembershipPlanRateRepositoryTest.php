<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories\Subscription;

use App\Models\Subscription\MembershipPlan;
use App\Models\Subscription\MembershipPlanRate;
use App\Repositories\Subscription\MembershipPlanRateRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class MembershipPlanRateRepositoryTest
 * @package Tests\Integration\Repositories\Subscription
 */
class MembershipPlanRateRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var MembershipPlanRateRepository
     */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->repository = new MembershipPlanRateRepository(
            new MembershipPlanRate(),
            $this->getGenericLogMock()
        );
    }

    public function testFindAllSuccess(): void
    {
        MembershipPlanRate::factory()->count(5)->create();
        $items = $this->repository->findAll();
        $this->assertCount(5, $items);
    }

    public function testFindAllEmpty(): void
    {
        $items = $this->repository->findAll();
        $this->assertEmpty($items);
    }

    public function testFindOrFailSuccess(): void
    {
        $model = MembershipPlanRate::factory()->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails(): void
    {
        MembershipPlanRate::factory()->create(['id' => 19]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(20);
    }

    public function testCreateSuccess(): void
    {
        $membershipPlan = MembershipPlan::factory()->create();
        /** @var MembershipPlanRate $membershipPlanRate */
        $membershipPlanRate = $this->repository->create([
            'cost' => 10.12,
            'active' => false,
        ], $membershipPlan);

        $this->assertEquals(10.12, $membershipPlanRate->cost);
        $this->assertEquals($membershipPlan->id, $membershipPlanRate->membership_plan_id);
    }

    public function testUpdateSuccess(): void
    {
        $model = MembershipPlanRate::factory()->create([
            'active' => 1,
        ]);
        $this->repository->update($model, [
            'active' => 0,
        ]);

        /** @var MembershipPlanRate $updated */
        $updated = MembershipPlanRate::find($model->id);
        $this->assertNotTrue($updated->active);
    }

    public function testDeleteSuccess(): void
    {
        $model = MembershipPlanRate::factory()->create();

        $this->repository->delete($model);

        $this->assertNull(MembershipPlanRate::find($model->id));
    }
}
