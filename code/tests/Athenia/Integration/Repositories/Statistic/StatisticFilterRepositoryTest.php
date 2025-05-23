<?php
declare(strict_types=1);

namespace Tests\Athenia\Integration\Repositories\Statistic;

use App\Models\Statistic\Statistic;
use App\Models\Statistic\StatisticFilter;
use App\Athenia\Repositories\Statistic\StatisticFilterRepository;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class StatisticFilterRepositoryTest
 * @package Tests\Athenia\Integration\Repositories\Statistics
 */
class StatisticFilterRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var StatisticFilterRepository
     */
    protected $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();

        $this->repository = new StatisticFilterRepository(
            new StatisticFilter(),
            $this->getGenericLogMock()
        );
    }

    public function testFindAllReturnsCollection()
    {
        StatisticFilter::factory()->count(5)->create();

        $models = $this->repository->findAll();

        $this->assertCount(5, $models);
    }

    public function testFindReturnsModel()
    {
        $model = StatisticFilter::factory()->create();

        $foundModel = $this->repository->findOrFail($model->id);

        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailThrowsException()
    {
        StatisticFilter::factory()->create(['id' => 2]);

        $this->expectException(\Exception::class);

        $this->repository->findOrFail(1);
    }

    public function testCreateSuccess()
    {
        /** @var Statistic $statistic */
        $statistic = Statistic::factory()->create();

        /** @var StatisticFilter $statisticFilter */
        $statisticFilter = $this->repository->create([
            'field' => 'active',
            'operator' => '=',
            'value' => '1',
        ], $statistic);

        $this->assertCount(1, StatisticFilter::all());
        $this->assertEquals($statisticFilter->statistic_id, $statistic->id);
        $this->assertEquals('active', $statisticFilter->field);
        $this->assertEquals('=', $statisticFilter->operator);
        $this->assertEquals('1', $statisticFilter->value);
    }

    public function testUpdateSuccess()
    {
        /** @var StatisticFilter $statisticFilter */
        $statisticFilter = StatisticFilter::factory()->create([
            'field' => 'active',
        ]);

        /** @var StatisticFilter $result */
        $result = $this->repository->update($statisticFilter, [
            'field' => 'type',
        ]);

        $this->assertEquals('type', $result->field);
    }

    public function testDeleteSuccess()
    {
        $model = StatisticFilter::factory()->create();

        $this->repository->delete($model);

        $this->assertNull(StatisticFilter::find($model->id));
    }
} 