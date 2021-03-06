<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories\BallotItemOption;

use App\Models\User\User;
use App\Models\Vote\BallotItem;
use App\Models\Vote\BallotItemOption;
use App\Repositories\Vote\BallotItemOptionRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class BallotItemOptionRepositoryTest
 * @package Tests\Integration\Repositories\BallotItemOption
 */
class BallotItemOptionRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var BallotItemOptionRepository
     */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->repository = new BallotItemOptionRepository(
            new BallotItemOption(),
            $this->getGenericLogMock()
        );
    }

    public function testFindAllSuccess()
    {
        BallotItemOption::factory()->count(5)->create();
        $items = $this->repository->findAll();
        $this->assertCount(5, $items);
    }

    public function testFindAllEmpty()
    {
        $items = $this->repository->findAll();
        $this->assertEmpty($items);
    }

    public function testFindOrFailSuccess()
    {
        $model = BallotItemOption::factory()->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails()
    {
        BallotItemOption::factory()->create(['id' => 19]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(20);
    }

    public function testCreateSuccess()
    {
        /** @var BallotItem $ballotItem */
        $ballotItem = BallotItem::factory()->create();

        /** @var User $user */
        $user = User::factory()->create();

        /** @var BallotItemOption $ballotItemOption */
        $ballotItemOption = $this->repository->create([
            'subject_id' => $user->id,
            'subject_type' => 'user',
        ], $ballotItem);

        $this->assertEquals($user->id, $ballotItemOption->subject_id);
        $this->assertEquals('user', $ballotItemOption->subject_type);
        $this->assertEquals($ballotItem->id, $ballotItemOption->ballot_item_id);
    }

    public function testUpdateSuccess()
    {
        $model = BallotItemOption::factory()->create([
            'vote_count' => 1,
        ]);
        $this->repository->update($model, [
            'vote_count' => 2,
        ]);

        /** @var BallotItemOption $updated */
        $updated = BallotItemOption::find($model->id);
        $this->assertEquals(2, $updated->vote_count);
    }

    public function testDeleteSuccess()
    {
        $model = BallotItemOption::factory()->create();

        $this->repository->delete($model);

        $this->assertNull(BallotItemOption::find($model->id));
    }
}
