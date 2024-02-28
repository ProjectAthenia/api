<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories\User;

use App\Models\User\Contact;
use App\Models\User\User;
use App\Repositories\User\ContactRepository;
use App\Repositories\User\UserRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class ContactRepositoryTest
 * @package Tests\Integration\Repositories\User
 */
class ContactRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var Hasher
     */
    private $hasher;

    /**
     * @var UserRepository
     */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->hasher = $this->app->make(Hasher::class);

        $this->repository = new ContactRepository(
            new Contact(),
            $this->getGenericLogMock(),
        );
    }

    public function testFindAllSuccess(): void
    {
        Contact::factory()->count( 5)->create();
        $items = $this->repository->findAll();
        $this->assertCount(5, $items);
    }

    public function testFindAllSuccessWithUser(): void
    {
        $user = User::factory()->create();

        Contact::factory()->count(5)->create();

        Contact::factory()->count(4)->create([
            'requested_id' => $user->id,
        ]);
        Contact::factory()->count(3)->create([
            'initiated_by_id' => $user->id,
        ]);

        $items = $this->repository->findAll([], [], [], [], 10, [$user]);
        $this->assertCount(7, $items);
    }

    public function testFindAllEmpty(): void
    {
        $items = $this->repository->findAll();
        $this->assertEmpty($items);
    }

    public function testFindOrFailSuccess(): void
    {
        $model = Contact::factory()->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails(): void
    {
        Contact::factory()->create(['id' => 19]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(20);
    }

    public function testCreateSuccess(): void
    {
        $initiatedBy = User::factory()->create();
        $requested = User::factory()->create();

        /** @var Contact $contact */
        $contact = $this->repository->create([
            'initiated_by_id' => $initiatedBy->id,
            'requested_id' => $requested->id,
        ]);

        $this->assertEquals(1, Contact::count());
        $this->assertEquals($initiatedBy->id, $contact->initiated_by_id);
        $this->assertEquals($requested->id, $contact->requested_id);
    }

    public function testUpdateSuccess(): void
    {
        $model = Contact::factory()->create();
        $this->repository->update($model, [
            'denied_at' => Carbon::now(),
        ]);

        $updated = Contact::find($model->id);
        $this->assertNotNull($updated->denied_at);
    }

    public function testDeleteSuccess(): void
    {
        $model = Contact::factory()->create();

        $this->repository->delete($model);

        $this->assertNull(Contact::find($model->id));
    }
}
