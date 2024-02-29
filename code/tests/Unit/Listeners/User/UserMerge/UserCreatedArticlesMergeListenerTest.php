<?php
declare(strict_types=1);

namespace Tests\Unit\Listeners\User\UserMerge;

use App\Contracts\Repositories\Wiki\ArticleRepositoryContract;
use App\Events\User\UserMergeEvent;
use App\Listeners\User\UserMerge\UserCreatedArticlesMergeListener;
use App\Models\User\User;
use App\Models\Wiki\Article;
use Illuminate\Support\Collection;
use Tests\CustomMockInterface;
use Tests\TestCase;

/**
 * Class UserCreatedArticlesMergeListenerTest
 * @package Tests\Unit\Listeners\User\UserMerge
 */
final class UserCreatedArticlesMergeListenerTest extends TestCase
{
    /**
     * @var ArticleRepositoryContract|CustomMockInterface
     */
    private $repository;

    /**
     * @var UserCreatedArticlesMergeListener
     */
    private $listener;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = mock(ArticleRepositoryContract::class);
        $this->listener = new UserCreatedArticlesMergeListener($this->repository);
    }

    public function testHandleWithoutMerge(): void
    {
        $mainUser = new User([
            'email' => 'test@test.com',
        ]);
        $mainUser->id = 564534;

        $mergeUser = new User([
            'email' => 'testy@test.com',
        ]);

        $event = new UserMergeEvent($mainUser, $mergeUser);

        $this->listener->handle($event);
    }

    public function testHandleWithMerge(): void
    {
        $mainUser = new User([
            'email' => 'test@test.com',
        ]);
        $mainUser->id = 564534;

        $mergeUser = new User([
            'email' => 'testy@test.com',
            'createdArticles' => new Collection([
                new Article()
            ])
        ]);

        $event = new UserMergeEvent($mainUser, $mergeUser, [
            'created_articles' => true,
        ]);

        $this->repository->shouldReceive('update')->once()->with($mergeUser->createdArticles->first(), [
            'created_by_id' => $mainUser->id,
        ]);

        $this->listener->handle($event);
    }
}