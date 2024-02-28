<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Collection\CollectionItem;

use App\Models\Collection\Collection;
use App\Models\Wiki\Article;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class UserPaymentMethodCreateTest
 * @package Tests\Feature\Http\User\PaymentMethod
 */
class CollectionCollectionItemCreateTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var string
     */
    private $path = '/v1/collections/';

    /**
     * @var Collection
     */
    private Collection $collection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();

        $this->collection = Collection::factory()->create();

        $this->path.= $this->collection->id . '/items';
    }

    public function testNotLoggedInUserBlocked(): void
    {
        $response = $this->json('POST', $this->path);

        $response->assertStatus(403);
    }

    public function testIncorrectUserBlocked(): void
    {
        $this->actAsUser();
        $response = $this->json('POST', $this->path);

        $response->assertStatus(403);
    }

    public function testCreateSuccessful(): void
    {
        $this->actingAs($this->collection->owner);

        $article = Article::factory()->create();

        $data = [
            'item_type' => 'article',
            'item_id' => $article->id,
            'order' => 4,
        ];
        $response = $this->json('POST', $this->path, $data);

        $response->assertStatus(201);

        $response->assertJson($data);
    }

    public function testCreateFailsRequiredFieldsNotPresent(): void
    {
        $this->actingAs($this->collection->owner);

        $response = $this->json('POST', $this->path);

        $response->assertStatus(400);

        $response->assertJson([
            'errors' => [
                'item_type' => ['The item type field is required.'],
                'item_id' => ['The item id field is required.'],
                'order' => ['The order field is required.'],
            ]
        ]);
    }

    public function testCreateFailsInvalidEnumFields(): void
    {
        $this->actingAs($this->collection->owner);

        $response = $this->json('POST', $this->path, [
            'item_type' => 'user',
        ]);

        $response->assertStatus(400);

        $response->assertJson([
            'errors' => [
                'item_type' => ['The selected item type is invalid.'],
            ]
        ]);
    }

    public function testCreateFailsInvalidIntegerFields(): void
    {
        $this->actingAs($this->collection->owner);

        $response = $this->json('POST', $this->path, [
            'item_id' => 'hello',
            'order' => 'hello',
        ]);

        $response->assertStatus(400);

        $response->assertJson([
            'errors' => [
                'item_id' => ['The item id must be an integer.'],
                'order' => ['The order must be an integer.'],
            ]
        ]);
    }
}
