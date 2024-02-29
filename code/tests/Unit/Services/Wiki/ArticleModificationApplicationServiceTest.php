<?php
declare(strict_types=1);

namespace Tests\Unit\Services\Wiki;

use App\Contracts\Repositories\Wiki\ArticleIterationRepositoryContract;
use App\Models\User\User;
use App\Models\Wiki\Article;
use App\Models\Wiki\ArticleModification;
use App\Services\StringHelperService;
use App\Services\Wiki\ArticleModificationApplicationService;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use Tests\CustomMockInterface;
use Tests\TestCase;

/**
 * Class ArticleModificationApplicationServiceTest
 * @package Tests\Unit\Services\Wiki
 */
final class ArticleModificationApplicationServiceTest extends TestCase
{
    /**
     * @var ArticleIterationRepositoryContract|array|LegacyMockInterface|MockInterface|CustomMockInterface
     */
    private $iterationRepository;

    /**
     * @var ArticleModificationApplicationService
     */
    private ArticleModificationApplicationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->iterationRepository = mock(ArticleIterationRepositoryContract::class);
        $this->service = new ArticleModificationApplicationService(
            $this->iterationRepository,
            new StringHelperService(),
        );
    }

    public function testApplyModificationRemove(): void
    {
        $user = new User();
        $user->id = 23;
        $article = new Article([
            'last_iteration_content' => 'This is a removal test of iugwhw something.'
        ]);
        $articleModification = new ArticleModification([
            'action' => 'remove',
            'start_position' => 26,
            'length' => 7,
            'article' => $article,
        ]);

        $this->iterationRepository->shouldReceive('create')->once()->with([
            'content' => 'This is a removal test of something.',
            'created_by_id' => 23,
        ], $article);

        $this->service->applyModification($user, $articleModification);
    }

    public function testApplyModificationAdd(): void
    {
        $user = new User();
        $user->id = 23;
        $article = new Article([
            'last_iteration_content' => 'This is a removal test of .'
        ]);
        $articleModification = new ArticleModification([
            'action' => 'add',
            'content' => 'this',
            'start_position' => 26,
            'article' => $article,
        ]);

        $this->iterationRepository->shouldReceive('create')->once()->with([
            'content' => 'This is a removal test of this.',
            'created_by_id' => 23,
        ], $article);

        $this->service->applyModification($user, $articleModification);
    }

    public function testApplyModificationReplace(): void
    {
        $user = new User();
        $user->id = 23;
        $article = new Article([
            'last_iteration_content' => 'This is a removal test of something.'
        ]);
        $articleModification = new ArticleModification([
            'action' => 'replace',
            'content' => 'this',
            'start_position' => 26,
            'length' => 9,
            'article' => $article,
        ]);

        $this->iterationRepository->shouldReceive('create')->once()->with([
            'content' => 'This is a removal test of this.',
            'created_by_id' => 23,
        ], $article);

        $this->service->applyModification($user, $articleModification);
    }
}
