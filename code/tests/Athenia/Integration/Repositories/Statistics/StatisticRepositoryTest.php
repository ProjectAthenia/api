<?php
declare(strict_types=1);

namespace Tests\Athenia\Integration\Repositories\Statistics;

use App\Athenia\Events\Statistics\StatisticCreatedEvent;
use App\Athenia\Events\Statistics\StatisticUpdatedEvent;
use App\Models\Statistics\Statistic;
use App\Models\Statistics\StatisticFilter;
use App\Athenia\Repositories\Statistics\StatisticFilterRepository;
use App\Athenia\Repositories\Statistics\StatisticRepository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Event;
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
    private $repository;

    /**
     * @var StatisticFilterRepository
     */
    private $statisticFilterRepository;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->dispatcher = app(Dispatcher::class);
        $this->statisticFilterRepository = app(StatisticFilterRepository::class);
        $this->repository = new StatisticRepository(
            app(Statistic::class),
            $this->getGenericLogMock(),
            $this->statisticFilterRepository,
            $this->dispatcher
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
        Statistic::factory()->create(['id' => 1]);
        Statistic::factory()->count(4)->create();

        $models = $this->repository->findAll(['id' => 1]);

        $this->assertCount(1, $models);
    }

    public function testFindReturnsModel()
    {
        foreach (Statistic::all() as $model) {
            $model->delete();
        }
        $model = Statistic::factory()->create();

        $foundModel = $this->repository->findOrFail($model->id);

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

    /**
     * @test
     */
    public function it_can_create_a_statistic_with_filters()
    {
        $data = [
            'name' => 'Test Statistic',
            'model' => 'User',
            'relation' => 'contacts',
            'public' => true,
            'statistic_filters' => [
                [
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
        ];

        Event::fake();

        $statistic = $this->repository->create($data);

        $this->assertInstanceOf(Statistic::class, $statistic);
        $this->assertEquals('Test Statistic', $statistic->name);
        $this->assertEquals('User', $statistic->model);
        $this->assertEquals('contacts', $statistic->relation);
        $this->assertTrue($statistic->public);

        $this->assertCount(2, $statistic->statisticFilters);
        $this->assertInstanceOf(Collection::class, $statistic->statisticFilters);

        $filter1 = $statistic->statisticFilters->first();
        $this->assertInstanceOf(StatisticFilter::class, $filter1);
        $this->assertEquals('active', $filter1->field);
        $this->assertEquals('=', $filter1->operator);
        $this->assertEquals('1', $filter1->value);

        $filter2 = $statistic->statisticFilters->last();
        $this->assertInstanceOf(StatisticFilter::class, $filter2);
        $this->assertEquals('type', $filter2->field);
        $this->assertEquals('=', $filter2->operator);
        $this->assertEquals('character', $filter2->value);

        Event::assertDispatched(StatisticCreatedEvent::class, function ($event) use ($statistic) {
            return $event->statistic->id === $statistic->id;
        });
    }

    /**
     * @test
     */
    public function it_can_update_a_statistic_with_filters()
    {
        $statistic = Statistic::factory()->create();
        StatisticFilter::factory()->count(2)->create([
            'statistic_id' => $statistic->id,
        ]);

        $data = [
            'name' => 'Updated Statistic',
            'public' => false,
            'statistic_filters' => [
                [
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
        ];

        Event::fake();

        $updatedStatistic = $this->repository->update($statistic, $data);

        $this->assertInstanceOf(Statistic::class, $updatedStatistic);
        $this->assertEquals('Updated Statistic', $updatedStatistic->name);
        $this->assertFalse($updatedStatistic->public);

        $this->assertCount(2, $updatedStatistic->statisticFilters);
        $this->assertInstanceOf(Collection::class, $updatedStatistic->statisticFilters);

        $filter1 = $updatedStatistic->statisticFilters->first();
        $this->assertInstanceOf(StatisticFilter::class, $filter1);
        $this->assertEquals('active', $filter1->field);
        $this->assertEquals('=', $filter1->operator);
        $this->assertEquals('1', $filter1->value);

        $filter2 = $updatedStatistic->statisticFilters->last();
        $this->assertInstanceOf(StatisticFilter::class, $filter2);
        $this->assertEquals('type', $filter2->field);
        $this->assertEquals('=', $filter2->operator);
        $this->assertEquals('character', $filter2->value);

        Event::assertDispatched(StatisticUpdatedEvent::class, function ($event) use ($updatedStatistic) {
            return $event->statistic->id === $updatedStatistic->id;
        });
    }

    public function testDeleteSuccess()
    {
        $model = Statistic::factory()->create();

        $this->repository->delete($model);

        $this->assertNull(Statistic::find($model->id));
    }
} 