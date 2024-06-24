<?php
declare(strict_types=1);

namespace Tests\Athenia\Feature\Http\Message;

use App\Models\Messaging\Message;
use App\Models\Role;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class MembershipPlanCreateTest
 * @package Tests\Athenia\Feature\Http\Category
 */
final class MessageCreateTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog, RolesTesting;
    
    private $route = '/v1/messages';

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testCreateSuccessful(): void
    {
        $properties = [
            'template' => 'contact',
            'data' => [
                'first_name' => 'John',
                'last_name' => 'Clancy',
                'phone' => '123',
            ],
        ];

        $response = $this->json('POST', $this->route, $properties);

        $response->assertStatus(201);

        $response->assertJson($properties);
    }

    public function testCreateSuccessfulConnectsLoggedInUser(): void
    {
        $this->actAs(Role::APP_USER);

        $properties = [
            'template' => 'contact',
            'data' => [
                'first_name' => 'John',
                'last_name' => 'Clancy',
                'phone' => '123',
            ],
        ];

        $response = $this->json('POST', $this->route, $properties);

        $response->assertStatus(201);

        $response->assertJson($properties);

        /** @var Message $model */
        $model = $response->original;

        $this->assertEquals($this->actingAs->id, $model->from_id);
        $this->assertEquals('user', $model->from_type);
    }

    public function testCreateFailsInvalidStringFields(): void
    {
        $data = [
            'message' => 324,
        ];

        $response = $this->json('POST', $this->route, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'message' => ['The message must be a string.'],
            ]
        ]);
    }

    public function testCreateFailsInvalidEnumFields(): void
    {
        $data = [
            'template' => 'bye',
        ];

        $response = $this->json('POST', $this->route, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'template' => ['The selected template is invalid.'],
            ]
        ]);
    }

    public function testCreateFailsInvalidBooleanFields(): void
    {
        $data = [
            'seen' => 'hello',
        ];

        $response = $this->json('POST', $this->route, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'seen' => ['The seen field must be true or false.'],
            ]
        ]);
    }

    public function testCreateFailsInvaliArrayFields(): void
    {
        $data = [
            'data' => 'hello',
        ];

        $response = $this->json('POST', $this->route, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'data' => ['The data must be an array.'],
            ]
        ]);
    }
}
