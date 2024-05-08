<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories\Messaging;

use App\Exceptions\NotImplementedException;
use App\Models\Messaging\Thread;
use App\Models\User\User;
use App\Repositories\Messaging\ThreadRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class ThreadRepositoryTest
 * @package Tests\Integration\Repositories\User
 */
final class ThreadRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var ThreadRepository
     */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->repository = new ThreadRepository(
            new Thread(),
            $this->getGenericLogMock(),
        );
    }

    public function testFindAllSuccess(): void
    {
        foreach (Thread::all() as $resource) {
            $resource->delete();
        }

        Thread::factory()->count(5)->create();
        $items = $this->repository->findAll();
        $this->assertCount(5, $items);
    }

    public function testFindAllEmpty(): void
    {
        foreach (Thread::all() as $resource) {
            $resource->delete();
        }

        $items = $this->repository->findAll();
        $this->assertEmpty($items);
    }

    public function testFindOrFailSuccess(): void
    {
        $model = Thread::factory()->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails(): void
    {
        Thread::factory()->create(['id' => 19]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(20);
    }

    public function testCreateSuccess(): void
    {
        $users = User::factory()->count(2)->create();

        /** @var Thread $thread */
        $thread = $this->repository->create([
            'users' => $users->pluck('id'),
        ]);

        $this->assertCount(2, $thread->users);
    }

    public function testUpdateFails(): void
    {
        $this->expectException(NotImplementedException::class);

        $this->repository->update(new Thread(), []);
    }

    public function testDeleteSuccess(): void
    {
        $model = Thread::factory()->create();

        $this->repository->delete($model);

        $this->assertNull(Thread::find($model->id));
    }
}
