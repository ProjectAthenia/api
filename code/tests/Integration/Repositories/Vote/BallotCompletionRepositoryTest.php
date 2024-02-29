<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories\Vote;

use App\Exceptions\NotImplementedException;
use App\Models\User\User;
use App\Models\Vote\Ballot;
use App\Models\Vote\BallotCompletion;
use App\Models\Vote\Vote;
use App\Repositories\Vote\BallotCompletionRepository;
use App\Repositories\Vote\VoteRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class BallotCompletionRepositoryTest
 * @package Tests\Integration\Repositories\Vote
 */
final class BallotCompletionRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var BallotCompletionRepository
     */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->repository = new BallotCompletionRepository(
            new BallotCompletion(),
            $this->getGenericLogMock(),
            new VoteRepository(
                new Vote(),
                $this->getGenericLogMock(),
            ),
        );
    }

    public function testFindAllSuccess(): void
    {
        BallotCompletion::factory()->count(5)->create();
        $items = $this->repository->findAll();
        $this->assertCount(5, $items);
    }

    public function testFindAllEmpty(): void
    {
        $items = $this->repository->findAll();
        $this->assertEmpty($items);
    }

    public function testFindOrFailSuccess(): void
    {
        $model = BallotCompletion::factory()->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails(): void
    {
        BallotCompletion::factory()->create(['id' => 19]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(20);
    }

    public function testCreateSuccess(): void
    {
        /** @var Ballot $ballot */
        $ballot = Ballot::factory()->create();

        /** @var User $user */
        $user = User::factory()->create();

        /** @var BallotCompletion $ballotCompletion */
        $ballotCompletion = $this->repository->create([
            'user_id' => $user->id,
        ], $ballot);

        $this->assertEquals($ballotCompletion->user_id, $user->id);
        $this->assertEquals($ballotCompletion->ballot_id, $ballot->id);
    }

    public function testUpdateThrowsException(): void
    {
        $this->expectException(NotImplementedException::class);
        $this->repository->update(new BallotCompletion(), []);
    }

    public function testDeleteSuccess(): void
    {
        $model = BallotCompletion::factory()->create();

        $this->repository->delete($model);

        $this->assertNull(BallotCompletion::find($model->id));
    }
}
