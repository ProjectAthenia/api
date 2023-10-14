<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Category;

use App\Models\Role;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class MembershipPlanCreateTest
 * @package Tests\Feature\Http\Category
 */
class CategoryCreateTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog, RolesTesting;
    
    private $route = '/v1/categories';

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testNotLoggedInUserBlocked()
    {
        $response = $this->json('POST', $this->route);
        $response->assertStatus(403);
    }

    public function testCreateSuccessful()
    {
        $this->actAs(Role::APP_USER);
        
        $properties = [
            'name' => 'A Category',
        ];

        $response = $this->json('POST', $this->route, $properties);

        $response->assertStatus(201);

        $response->assertJson($properties);
    }

    public function testCreateFailsMissingRequiredFields()
    {
        $this->actAs(Role::APP_USER);

        $response = $this->json('POST', $this->route);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'name' => ['The name field is required.'],
            ]
        ]);
    }

    public function testCreateFailsInvalidStringFields()
    {
        $this->actAs(Role::APP_USER);

        $data = [
            'name' => 5435,
            'description' => 5,
        ];

        $response = $this->json('POST', $this->route, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'name' => ['The name must be a string.'],
                'description' => ['The description must be a string.'],
            ]
        ]);
    }
}
