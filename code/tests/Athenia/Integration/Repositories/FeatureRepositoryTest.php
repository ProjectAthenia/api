<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories;

use App\Athenia\Repositories\FeatureRepository;
use App\Models\Feature;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class ResourceRepositoryTest
 * @package Tests\Integration\Repositories
 */
final class FeatureRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var FeatureRepository
     */
    protected FeatureRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->repository = new FeatureRepository(
            new Feature(),
            $this->getGenericLogMock()
        );
    }

    public function testFindAllSuccess(): void
    {
        Feature::factory()->count(5)->create();
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
        $model = Feature::factory()->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails(): void
    {
        Feature::factory()->create(['id' => 19]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(20);
    }

    public function testCreateSuccess(): void
    {
        /** @var Feature $feature */
        $feature = $this->repository->create([
            'name' => 'A Feature',
        ]);

        $this->assertEquals('A Feature', $feature->name);
    }

    public function testUpdateSuccess(): void
    {
        $model = Feature::factory()->create([
            'name' => 'a code'
        ]);
        $this->repository->update($model, [
            'name' => 'the same',
        ]);

        $updated = Feature::find($model->id);
        $this->assertEquals('the same', $updated->name);
    }

    public function testDeleteSuccess(): void
    {
        $model = Feature::factory()->create();

        $this->repository->delete($model);

        $this->assertNull(Feature::find($model->id));
    }
}
