<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class RoleRepositoryTest
 * @package Tests\Integration\Repositories
 */
class CategoryRepositoryTest extends TestCase
{
    use DatabaseSetupTrait;
    
    /**
     * @var CategoryRepository
     */
    protected $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        
        $this->repository = new CategoryRepository(new Category(), $this->getGenericLogMock());
    }

    public function testFindAllSuccess()
    {
        Category::factory()->count( 5)->create();
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
        $model = Category::factory()->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails()
    {
        Category::factory()->create(['id' => 19]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(20);
    }

    public function testCreateSuccess()
    {
        /** @var Category $category */
        $category = $this->repository->create([
            'name' => 'A Category',
        ]);

        $this->assertEquals('A Category', $category->name);
    }

    public function testUpdateSuccess()
    {
        $model = Category::factory()->create([
            'name' => 'An Category',
        ]);
        $this->repository->update($model, [
            'name' => 'A Category',
        ]);

        $updated = Category::find($model->id);
        $this->assertEquals('A Category', $updated->name);
    }

    public function testDeleteSuccess()
    {
        $model = Category::factory()->create();

        $this->repository->delete($model);

        $this->assertNull(Category::find($model->id));
    }
}
