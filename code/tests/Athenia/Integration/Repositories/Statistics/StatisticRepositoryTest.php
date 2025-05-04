<?php
declare(strict_types=1);

namespace Tests\Athenia\Integration\Repositories\Statistics;

use App\Athenia\Events\Statistics\StatisticUpdatedEvent;
use App\Models\Statistics\Statistic;
use App\Models\Statistics\StatisticFilter;
use App\Athenia\Repositories\Statistics\StatisticFilterRepository;
use App\Athenia\Repositories\Statistics\StatisticRepository;
use Illuminate\Contracts\Events\Dispatcher;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class StatisticRepositoryTest
 * @package Tests\Athenia\Integration\Repositories\Statistics
 */
class StatisticRepositoryTest extends TestCase
{
    use DatabaseSetupTrait;

    /**
     * @var StatisticRepository
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

        $this->repository = new StatisticRepository(
            new Statistic(),
            $this->dispatcher,
            new StatisticFilterRepository(
                new StatisticFilter(),
                $this->dispatcher
            )
        );
    }

    public function testFindAllReturnsCollection()
    {
        foreach (Statistic::all() as $model) {
            $model->delete();
        }
        Statistic::factory()->count(5)->create();

        $models = $this->repository->findAll();

        $this->assertCount(5, $models);
    }

    public function testFindAllWithFilterReturnsCollection()
    {
        foreach (Statistic::all() as $model) {
            $model->delete();
        }
        Statistic::factory()->count(5)->create();

        $models = $this->repository->findAll(['id' => 1]);

        $this->assertCount(1, $models);
    }

    public function testFindReturnsModel()
    {
        foreach (Statistic::all() as $model) {
            $model->delete();
        }
        $model = Statistic::factory()->create();

        $foundModel = $this->repository->find($model->id);

        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailThrowsException()
    {
        foreach (Statistic::all() as $model) {
            $model->delete();
        }
        Statistic::factory()->create(['id' => 35]);

        $this->expectException(\Exception::class);

        $this->repository->findOrFail(1);
    }

    public function testCreateSuccess()
    {
        $this->dispatcher->shouldReceive('dispatch')->once()->with(\Mockery::on(function (StatisticUpdatedEvent $event) {
            return true;
        }));

        /** @var Statistic $statistic */
        $statistic = $this->repository->create([
            'type' => 'characters',
            'name' => 'Test',
        ]);

        $this->assertEquals('characters', $statistic->type);
    }

    public function testUpdateSuccessWithoutStatisticFilters()
    {
        $statistic = Statistic::factory()->create([
            'type' => 'characters',
        ]);

        StatisticFilter::factory()->count(3)->create([
            'statistic_id' => $statistic->id,
        ]);

        /** @var Statistic $result */
        $result = $this->repository->update($statistic, [
            'type' => 'words',
            'name' => 'Test',
        ]);

        $this->assertEquals('words', $result->type);
        $this->assertCount(3, $result->statisticFilters);
    }

    public function testUpdateSuccessWithStatisticFilters()
    {
        $statistic = Statistic::factory()->create();

        $existingFilters = StatisticFilter::factory()->count(3)->create([
            'statistic_id' => $statistic->id,
        ]);

        $this->dispatcher->shouldReceive('dispatch')->once()->with(\Mockery::on(function (StatisticUpdatedEvent $event) {
            return true;
        }));

        /** @var Statistic $result */
        $result = $this->repository->update($statistic, [
            'statistic_filters' => [
                [
                    'id' => $existingFilters[0]->id,
                    'field' => 'active',
                    'operator' => '=',
                    'value' => '1',
                ],
                [
                    'field' => 'type',
                    'operator' => '=',
                    'value' => 'character',
                ],
            ],
        ]);

        $this->assertCount(2, $result->statisticFilters);
    }

    public function testDeleteSuccess()
    {
        $model = Statistic::factory()->create();

        $this->repository->delete($model);

        $this->assertNull(Statistic::find($model->id));
    }

    public function testFindByTypeReturnsCollection()
    {
        Statistic::factory()->count(5)->create();
        Statistic::factory()->count(3)->create([
            'type' => 'character',
        ]);

        $models = $this->repository->findByType('character');

        $this->assertCount(3, $models);
    }
} 