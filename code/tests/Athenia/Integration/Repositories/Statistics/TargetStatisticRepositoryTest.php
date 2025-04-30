<?php
declare(strict_types=1);

namespace Tests\Athenia\Integration\Repositories\Statistics;

use App\Models\Statistics\TargetStatistic;
use App\Models\Statistics\Statistic;
use App\Models\User;
use App\Athenia\Repositories\Statistics\TargetStatisticRepository;
use Illuminate\Contracts\Events\Dispatcher;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class TargetStatisticRepositoryTest
 * @package Tests\Athenia\Integration\Repositories\Statistics
 */
class TargetStatisticRepositoryTest extends TestCase
{
    use DatabaseSetupTrait;

    /**
     * @var TargetStatisticRepository
     */
    protected $repository;

    /**
     * @var Dispatcher|\Mockery\LegacyMockInterface|\Mockery\MockInterface
     */
    private $dispatcher;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->dispatcher = mock(Dispatcher::class);
        $this->repository = new TargetStatisticRepository(
            new TargetStatistic(),
            $this->dispatcher
        );
    }

    public function testCreateForTargetSuccess()
    {
        $user = User::factory()->create();
        $statistic = Statistic::factory()->create();

        $targetStatistic = $this->repository->createForTarget($user, [
            'statistic_id' => $statistic->id,
            'value' => 42.5,
            'filters' => ['type' => 'test'],
        ]);

        $this->assertEquals($user->id, $targetStatistic->target_id);
        $this->assertEquals(User::class, $targetStatistic->target_type);
        $this->assertEquals($statistic->id, $targetStatistic->statistic_id);
        $this->assertEquals(42.5, $targetStatistic->value);
        $this->assertEquals(['type' => 'test'], $targetStatistic->filters);
    }

    public function testFindAllForTargetSuccess()
    {
        $user = User::factory()->create();
        TargetStatistic::factory()->count(3)->create([
            'target_id' => $user->id,
            'target_type' => User::class,
        ]);
        // Create some stats for another user to ensure filtering works
        TargetStatistic::factory()->count(2)->create();

        $results = $this->repository->findAllForTarget($user);

        $this->assertCount(3, $results);
        foreach ($results as $stat) {
            $this->assertEquals($user->id, $stat->target_id);
            $this->assertEquals(User::class, $stat->target_type);
        }
    }

    public function testFindForTargetSuccess()
    {
        $user = User::factory()->create();
        $statistic = Statistic::factory()->create();
        TargetStatistic::factory()->create([
            'target_id' => $user->id,
            'target_type' => User::class,
            'statistic_id' => $statistic->id,
        ]);

        $result = $this->repository->findForTarget($user, $statistic->id);

        $this->assertNotNull($result);
        $this->assertEquals($user->id, $result->target_id);
        $this->assertEquals($statistic->id, $result->statistic_id);
    }

    public function testFindForTargetReturnsNullWhenNotFound()
    {
        $user = User::factory()->create();
        $result = $this->repository->findForTarget($user, 999);

        $this->assertNull($result);
    }
} 