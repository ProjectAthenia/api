<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories\Vote;

use App\Athenia\Repositories\Vote\BallotItemOptionRepository;
use App\Athenia\Repositories\Vote\BallotItemRepository;
use App\Models\Vote\Ballot;
use App\Models\Vote\BallotItem;
use App\Models\Vote\BallotItemOption;
use App\Models\Wiki\ArticleIteration;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class BallotItemRepositoryTest
 * @package Tests\Integration\Repositories\Vote
 */
final class BallotItemRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var BallotItemRepository
     */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->repository = new BallotItemRepository(
            new BallotItem(),
            $this->getGenericLogMock(),
            new BallotItemOptionRepository(
                new BallotItemOption(),
                $this->getGenericLogMock(),
            ),
        );
    }

    public function testFindAllSuccess(): void
    {
        BallotItem::factory()->count(5)->create();
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
        $model = BallotItem::factory()->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails(): void
    {
        BallotItem::factory()->create(['id' => 19]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(20);
    }

    public function testCreateSuccess(): void
    {
        /** @var Ballot $ballot */
        $ballot = Ballot::factory()->create();

        /** @var ArticleIteration $iteration */
        $iteration = ArticleIteration::factory()->create();

        /** @var BallotItem $ballotItem */
        $ballotItem = $this->repository->create([
            'ballot_item_options' => [
                [
                    'subject_id' => $iteration->id,
                    'subject_type' => 'iteration',
                ]
            ]
        ], $ballot);

        $this->assertCount(1, $ballotItem->ballotItemOptions);
        $this->assertEquals($ballotItem->ballotItemOptions[0]->subject_id, $iteration->id);
        $this->assertEquals($ballotItem->ballot_id, $ballot->id);
    }

    public function testUpdateSuccess(): void
    {
        $model = BallotItem::factory()->create([
            'votes_cast' => 1,
        ]);
        $this->repository->update($model, [
            'votes_cast' => 5,
        ]);

        /** @var BallotItem $updated */
        $updated = BallotItem::find($model->id);
        $this->assertEquals(5, $updated->votes_cast);
    }

    public function testDeleteSuccess(): void
    {
        $model = BallotItem::factory()->create();

        $this->repository->delete($model);

        $this->assertNull(BallotItem::find($model->id));
    }
}
