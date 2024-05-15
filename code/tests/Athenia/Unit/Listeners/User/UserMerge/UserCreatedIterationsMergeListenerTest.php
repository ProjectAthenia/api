<?php
declare(strict_types=1);

namespace Tests\Unit\Listeners\User\UserMerge;

use App\Athenia\Contracts\Repositories\Wiki\ArticleIterationRepositoryContract;
use App\Athenia\Events\User\UserMergeEvent;
use App\Athenia\Listeners\User\UserMerge\UserCreatedIterationsMergeListener;
use App\Models\User\User;
use App\Models\Wiki\ArticleIteration;
use Illuminate\Support\Collection;
use Tests\CustomMockInterface;
use Tests\TestCase;

/**
 * Class UserCreatedIterationsMergeListenerTest
 * @package Tests\Unit\Listeners\User\UserMerge
 */
final class UserCreatedIterationsMergeListenerTest extends TestCase
{
    /**
     * @var ArticleIterationRepositoryContract|CustomMockInterface
     */
    private $repository;

    /**
     * @var UserCreatedIterationsMergeListener
     */
    private $listener;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = mock(ArticleIterationRepositoryContract::class);
        $this->listener = new UserCreatedIterationsMergeListener($this->repository);
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
            'createdIterations' => new Collection([
                new ArticleIteration()
            ])
        ]);

        $event = new UserMergeEvent($mainUser, $mergeUser, [
            'created_iterations' => true,
        ]);

        $this->repository->shouldReceive('update')->once()->with($mergeUser->createdIterations->first(), [
            'created_by_id' => $mainUser->id,
        ]);

        $this->listener->handle($event);
    }
}
