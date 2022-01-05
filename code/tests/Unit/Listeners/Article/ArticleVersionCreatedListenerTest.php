<?php
declare(strict_types=1);

namespace Tests\Unit\Article;

use App\Contracts\Repositories\Wiki\ArticleVersionRepositoryContract;
use App\Contracts\Services\Wiki\ArticleVersionCalculationServiceContract;
use App\Events\Article\ArticleVersionCreatedEvent;
use App\Listeners\Article\ArticleVersionCreatedListener;
use App\Models\Wiki\ArticleVersion;
use App\Models\Wiki\ArticleIteration;
use Tests\CustomMockInterface;
use Tests\TestCase;

/**
 * Class ArticleVersionCreatedListenerTest
 * @package Tests\Unit\Article
 */
class ArticleVersionCreatedListenerTest extends TestCase
{
    /**
     * @var ArticleVersionRepositoryContract|CustomMockInterface
     */
    private $repository;

    /**
     * @var ArticleVersionCalculationServiceContract|CustomMockInterface
     */
    private $calculationService;

    /**
     * @var ArticleVersionCreatedListener
     */
    private $listener;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = mock(ArticleVersionRepositoryContract::class);
        $this->calculationService = mock(ArticleVersionCalculationServiceContract::class);

        $this->listener = new ArticleVersionCreatedListener($this->repository, $this->calculationService);
    }

    public function testMajorVersion()
    {
        $oldVersion = new ArticleVersion([
            'name' => '12.45.23',
            'articleIteration' => new ArticleIteration([
                'content' => 'Some Content',
            ]),
        ]);
        $newVersion = new ArticleVersion([
            'articleIteration' => new ArticleIteration([
                'content' => 'Some new Content',
            ]),
        ]);

        $event = new ArticleVersionCreatedEvent($newVersion, $oldVersion);
        $this->calculationService->shouldReceive('determineIfMajor')->andReturnTrue();
        $this->repository->shouldReceive('update')->once()->with($newVersion, ['name' => '13.0.0']);

        $this->listener->handle($event);
    }

    public function testMinorVersion()
    {
        $oldVersion = new ArticleVersion([
            'name' => '12.45.23',
            'articleIteration' => new ArticleIteration([
                'content' => 'Some Content',
            ]),
        ]);
        $newVersion = new ArticleVersion([
            'articleIteration' => new ArticleIteration([
                'content' => 'Some new Content',
            ]),
        ]);

        $event = new ArticleVersionCreatedEvent($newVersion, $oldVersion);
        $this->calculationService->shouldReceive('determineIfMajor')->andReturnFalse();
        $this->calculationService->shouldReceive('determineIfMinor')->andReturnTrue();
        $this->repository->shouldReceive('update')->once()->with($newVersion, ['name' => '12.46.0']);

        $this->listener->handle($event);
    }

    public function testPatchVersion()
    {
        $oldVersion = new ArticleVersion([
            'name' => '12.45.23',
            'articleIteration' => new ArticleIteration([
                'content' => 'Some Content',
            ]),
        ]);
        $newVersion = new ArticleVersion([
            'articleIteration' => new ArticleIteration([
                'content' => 'Some new Content',
            ]),
        ]);

        $event = new ArticleVersionCreatedEvent($newVersion, $oldVersion);
        $this->calculationService->shouldReceive('determineIfMajor')->andReturnFalse();
        $this->calculationService->shouldReceive('determineIfMinor')->andReturnFalse();
        $this->repository->shouldReceive('update')->once()->with($newVersion, ['name' => '12.45.24']);

        $this->listener->handle($event);
    }
}
