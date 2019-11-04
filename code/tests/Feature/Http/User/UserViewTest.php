<?php
declare(strict_types=1);

namespace Tests\Feature\Http\User;

use App\Models\User\User;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class UserViewTest
 * @package Tests\Feature\Http\User
 */
class UserViewTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var string
     */
    private $path = '/v1/users/';

    /**
     * @var User
     */
    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();

        $this->user = factory(User::class)->create();
        $this->path.= $this->user->id;
    }

    public function testNotLoggedInUserBlocked()
    {
        $response = $this->json('GET', $this->path);

        $response->assertStatus(403);
    }

    public function testNotFound()
    {
        $this->actAsUser();

        $response = $this->json('GET',   '/v1/users/1435');

        $response->assertStatus(404);
    }

    public function testViewSuccessful()
    {
        $this->actAsUser();

        $response = $this->json('GET', $this->path);

        $response->assertStatus(200);

        $data = $this->user->toArray();
        unset($data['resource']);
        $response->assertJson($data);
    }
}