<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories\Subscription;

use App\Athenia\Repositories\Subscription\MembershipPlanRateRepository;
use App\Athenia\Repositories\Subscription\MembershipPlanRepository;
use App\Models\Feature;
use App\Models\Subscription\MembershipPlan;
use App\Models\Subscription\MembershipPlanRate;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class MembershipPlanRepositoryTest
 * @package Tests\Integration\Repositories\Subscription
 */
final class MembershipPlanRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var MembershipPlanRepository
     */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->repository = new MembershipPlanRepository(
            new MembershipPlan(),
            $this->getGenericLogMock(),
            new MembershipPlanRateRepository(
                new MembershipPlanRate(),
                $this->getGenericLogMock(),
            ),
        );
    }

    public function testFindAllSuccess(): void
    {
        foreach (MembershipPlan::all() as $model) {
            $model->delete();
        }

        MembershipPlan::factory()->count(5)->create();
        $items = $this->repository->findAll();
        $this->assertCount(5, $items);
    }

    public function testFindAllEmpty(): void
    {
        foreach (MembershipPlan::all() as $model) {
            $model->delete();
        }

        $items = $this->repository->findAll();
        $this->assertEmpty($items);
    }

    public function testFindOrFailSuccess(): void
    {
        $model = MembershipPlan::factory()->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails(): void
    {
        MembershipPlan::factory()->create(['id' => 19]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(20);
    }

    public function testCreateSuccess(): void
    {
        /** @var MembershipPlan $membershipPlan */
        $membershipPlan = $this->repository->create([
            'duration' => MembershipPlan::DURATION_YEAR,
            'name' => 'a plan',
        ]);

        $this->assertEquals(MembershipPlan::DURATION_YEAR, $membershipPlan->duration);
        $this->assertEquals('a plan', $membershipPlan->name);
    }

    public function testCreateSuccessWithCost(): void
    {
        /** @var MembershipPlan $membershipPlan */
        $membershipPlan = $this->repository->create([
            'duration' => MembershipPlan::DURATION_YEAR,
            'name' => 'a plan',
            'current_cost' => 10.12,
        ]);

        $this->assertEquals(MembershipPlan::DURATION_YEAR, $membershipPlan->duration);
        $this->assertEquals('a plan', $membershipPlan->name);
        $this->assertEquals(10.12, $membershipPlan->current_cost);
    }

    public function testCreateSuccessWithFeatures(): void
    {
        /** @var MembershipPlan $membershipPlan */
        $membershipPlan = $this->repository->create([
            'duration' => MembershipPlan::DURATION_YEAR,
            'name' => 'a plan',
            'features' => Feature::factory()->count(3)->create()->pluck('id'),
        ]);

        $this->assertEquals(MembershipPlan::DURATION_YEAR, $membershipPlan->duration);
        $this->assertEquals('a plan', $membershipPlan->name);
        $this->assertCount(3, $membershipPlan->features);
    }

    public function testUpdateSuccess(): void
    {
        $model = MembershipPlan::factory()->create([
            'name' => 'a plan'
        ]);
        $updated = $this->repository->update($model, [
            'name' => 'the same plan',
        ]);

        $this->assertEquals('the same plan', $updated->name);
    }

    public function testUpdateSuccessWithCost(): void
    {
        $model = MembershipPlan::factory()->create([
            'name' => 'a plan'
        ]);
        MembershipPlanRate::factory()->create([
            'cost' => 1.99,
            'membership_plan_id' => $model->id,
        ]);
        $updated = $this->repository->update($model, [
            'current_cost' => 3.99,
        ]);

        $this->assertEquals(3.99, $updated->current_cost);
    }

    public function testUpdateSuccessWithFeatures(): void
    {
        $model = MembershipPlan::factory()->create();
        $model->features()->sync(Feature::factory()->count(2)->create()->pluck('id'));

        $updated = $this->repository->update($model, [
            'features' => Feature::factory()->count(3)->create()->pluck('id'),
        ]);

        $this->assertCount(3, $updated->features);
    }

    public function testDeleteSuccess(): void
    {
        $model = MembershipPlan::factory()->create();

        $this->repository->delete($model);

        $this->assertNull(MembershipPlan::find($model->id));
    }

    public function testFindDefaultMembershipPlanForEntity(): void
    {
        $this->assertNull($this->repository->findDefaultMembershipPlanForEntity('user'));

        MembershipPlan::factory()->create([
            'entity_type' => 'user',
            'default' => 0,
        ]);
        $this->assertNull($this->repository->findDefaultMembershipPlanForEntity('user'));

        MembershipPlan::factory()->create([
            'entity_type' => 'user',
            'default' => 0,
        ]);
        $this->assertNull($this->repository->findDefaultMembershipPlanForEntity('organization'));

        MembershipPlan::factory()->create([
            'entity_type' => 'user',
            'default' => 1,
        ]);
        $this->assertNotNull($this->repository->findDefaultMembershipPlanForEntity('user'));
    }
}
