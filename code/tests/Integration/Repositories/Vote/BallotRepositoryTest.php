<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories\Vote;

use App\Models\Vote\Ballot;
use App\Models\Vote\BallotItem;
use App\Models\Vote\BallotItemOption;
use App\Models\Wiki\Iteration;
use App\Repositories\Vote\BallotItemOptionRepository;
use App\Repositories\Vote\BallotRepository;
use App\Repositories\Vote\BallotItemRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class VoteRepositoryTest
 * @package Tests\Integration\Repositories\Vote
 */
class BallotRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var BallotRepository
     */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->repository = new BallotRepository(
            new Ballot(),
            $this->getGenericLogMock(),
            new BallotItemRepository(
                new BallotItem(),
                $this->getGenericLogMock(),
                new BallotItemOptionRepository(
                    new BallotItemOption(),
                    $this->getGenericLogMock(),
                ),
            ),
        );
    }

    public function testFindAllSuccess()
    {
        Ballot::factory()->count(5)->create();
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
        $model = Ballot::factory()->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails()
    {
        Ballot::factory()->create(['id' => 19]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(20);
    }

    public function testCreateSuccess()
    {
        /** @var Ballot $ballot */
        $ballot = $this->repository->create([
            'type' => Ballot::TYPE_SINGLE_OPTION,
            'ballot_items' => [
                [
                    'ballot_item_options' => [
                        [
                            'subject_id' => Iteration::factory()->create()->id,
                            'subject_type' => 'iteration',
                        ],
                    ]
                ]
            ],
        ]);

        $this->assertEquals($ballot->type, Ballot::TYPE_SINGLE_OPTION);
        $this->assertCount(1, $ballot->ballotItems);
    }

    public function testUpdateSuccess()
    {
        $model = Ballot::factory()->create();
        $subjects = BallotItem::factory()->count(3)->create([
            'ballot_id' => $model->id,
        ]);

        $this->repository->update($model, [
            'ballot_items' => [
                [
                    'id' => $subjects[1]->id,
                    'ballot_item_options' => [
                        [
                            'subject_id' => Iteration::factory()->create()->id,
                            'subject_type' => 'iteration',
                        ]
                    ]
                ],
                [
                    'ballot_item_options' => [
                        [
                            'subject_id' => Iteration::factory()->create()->id,
                            'subject_type' => 'iteration',
                        ]
                    ]
                ],
            ],
        ]);

        /** @var Ballot $updated */
        $updated = Ballot::find($model->id);
        $this->assertCount(2, $updated->ballotItems);
    }

    public function testDeleteSuccess()
    {
        $model = factory(Ballot::class)->create();

        $this->repository->delete($model);

        $this->assertNull(Ballot::find($model->id));
    }
}
