<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories\Vote;

use App\Models\Vote\BallotCompletion;
use App\Models\Vote\BallotItem;
use App\Models\Vote\BallotItemOption;
use App\Models\Vote\Vote;
use App\Repositories\Vote\VoteRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class VoteRepositoryTest
 * @package Tests\Integration\Repositories\Vote
 */
class VoteRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var VoteRepository
     */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->repository = new VoteRepository(
            new Vote(),
            $this->getGenericLogMock()
        );
    }

    public function testFindAllSuccess(): void
    {
        Vote::factory()->count(5)->create();
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
        $model = Vote::factory()->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails(): void
    {
        Vote::factory()->create(['id' => 19]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(20);
    }

    public function testCreateSuccess(): void
    {
        /** @var BallotCompletion $ballotCompletion */
        $ballotCompletion = BallotCompletion::factory()->create();

        /** @var BallotItemOption $ballotItemOption */
        $ballotItemOption = BallotItemOption::factory()->create();

        /** @var Vote $vote */
        $vote = $this->repository->create([
            'ballot_item_option_id' => $ballotItemOption->id,
            'result' => 1,
        ], $ballotCompletion);

        $this->assertEquals(1, $vote->result);
        $this->assertEquals($ballotItemOption->id, $vote->ballot_item_option_id);
        $this->assertEquals($ballotCompletion->id, $vote->ballot_completion_id);
    }

    public function testUpdateSuccess(): void
    {
        $model = Vote::factory()->create([
            'result' => 1,
        ]);
        $this->repository->update($model, [
            'result' => 0,
        ]);

        /** @var Vote $updated */
        $updated = Vote::find($model->id);
        $this->assertEquals(0, $updated->result);
    }

    public function testDeleteSuccess(): void
    {
        $model = Vote::factory()->create();

        $this->repository->delete($model);

        $this->assertNull(Vote::find($model->id));
    }
}
