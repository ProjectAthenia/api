<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Observers;

use App\Athenia\Contracts\Models\CanBeStatisticTargetContract;
use App\Athenia\Jobs\Statistics\ProcessTargetStatisticsJob;
use App\Athenia\Observers\AggregatedModelObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class AggregatedModelObserverTest extends TestCase
{
    private AggregatedModelObserver $observer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->observer = new AggregatedModelObserver();
        Queue::fake();
    }

    /**
     * @dataProvider modelEventProvider
     */
    public function testModelEventsDispatchJobForStatisticTarget(string $event)
    {
        /** @var CanBeStatisticTargetContract|Model|MockInterface $model */
        $model = Mockery::mock(CanBeStatisticTargetContract::class, Model::class);

        $this->observer->$event($model);

        Queue::assertPushed(ProcessTargetStatisticsJob::class, function ($job) use ($model) {
            return $job->target === $model;
        });
    }

    /**
     * @dataProvider modelEventProvider
     */
    public function testModelEventsDoNotDispatchJobForNonStatisticTarget(string $event)
    {
        /** @var Model|MockInterface $model */
        $model = Mockery::mock(Model::class);

        $this->observer->$event($model);

        Queue::assertNotPushed(ProcessTargetStatisticsJob::class);
    }

    public function modelEventProvider(): array
    {
        return [
            'created event' => ['created'],
            'updated event' => ['updated'],
            'deleted event' => ['deleted'],
            'restored event' => ['restored'],
        ];
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
} 