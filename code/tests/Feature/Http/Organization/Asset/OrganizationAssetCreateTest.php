<?php
declare(strict_types=1);

namespace Tests\Feature\Organization\Asset;

use App\Models\Asset;
use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationManager;
use App\Models\Role;
use Illuminate\Support\Facades\Storage;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class UserAssetCreateTest
 * @package Tests\Feature\Organization\Asset
 */
class OrganizationAssetCreateTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var string
     */
    private $path = '/v1/organizations/';

    /**
     * @var Organization
     */
    private $organization;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();

        $this->organization = factory(Organization::class)->create();
        $this->path.= $this->organization->id . '/assets';

        Storage::fake('public');
    }

    public function testNotLoggedUserBlocked()
    {
        $response = $this->json('POST', $this->path);

        $response->assertStatus(403);
    }

    public function testNotRelatedUserBlocked()
    {
        $response = $this->json('POST', $this->path);

        $response->assertStatus(403);
    }

    public function testCreateSuccessful()
    {
        $this->actAs(Role::APP_USER);
        factory(OrganizationManager::class)->create([
            'role_id' => Role::MANAGER,
            'organization_id' => $this->organization->id,
            'user_id' => $this->actingAs->id,
        ]);

        $properties = [
            'name' => 'An Asset',
            'file_contents' => "iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAAABGdBTUEAALGPC/xhBQAAAAFzUkdCAK7OHOkAAAAgY0hSTQAAeiYAAICEAAD6AAAAgOgAAHUwAADqYAAAOpgAABdwnLpRPAAAAPBQTFRF////+/v77/Dx7e7vw8bL6evs/v7+zdDVd32Rd3qWdXiUZGiGf4Kdycfb4N7sxcnOaGeUQzmNQjiLQzmMQjiMQTiLeHGsxsrOZWSTOjCIOS+HOC2FcGmo/f39+/v8xMjMOzGIOjCHOC6GcWqoys3SanOBWGJyWGNyWWNzanOAZ2WUSFRlCBgwCRkxCBkwPkpbaGaVVWBvCRkw2tzfhIuXcHmHcXqHcnuId4CLZmSTNy2FxcnNZ2aTRDqMQzqLRDuMRz6PfHWv0tXZgIaXgIObfoGZaW6Iio2k1dTj5+bx/f3++Pj5+Pj49fb2ztHU8fLz/mE5kAAAAAFiS0dEAIgFHUgAAAAJcEhZcwAALiMAAC4jAXilP3YAAACLSURBVBjTY2DABRiZmJhZWNkQAuwcnFzcPLx8cAF+AUEhYRFRMbiAuISklJSUtAwDg6wcCLDJSygoKioqKTOoqKqpq6uraWhqQQW0dXRBQE/fACpgqGuEKmBsYmoGBOYWMC1wWyzRBcDWWsnABaxtbO3s7R0c4QJOzi6ubu4ennABL28fXz9/LL4GAJ3CFbkYlIJgAAAAJXRFWHRkYXRlOmNyZWF0ZQAyMDE4LTEyLTE3VDAwOjUwOjE1KzAwOjAwR3P0NQAAACV0RVh0ZGF0ZTptb2RpZnkAMjAxOC0xMi0xN1QwMDo1MDoxNSswMDowMDYuTIkAAAARdEVYdGpwZWc6Y29sb3JzcGFjZQAyLHVVnwAAACB0RVh0anBlZzpzYW1wbGluZy1mYWN0b3IAMXgxLDF4MSwxeDHplfxwAAAACnRFWHRyZGY6QWx0ACAgzYPMqwAAAEZ0RVh0c29mdHdhcmUASW1hZ2VNYWdpY2sgNi43LjgtOSAyMDE0LTA1LTEyIFExNiBodHRwOi8vd3d3LmltYWdlbWFnaWNrLm9yZ9yG7QAAAAAYdEVYdFRodW1iOjpEb2N1bWVudDo6UGFnZXMAMaf/uy8AAAAYdEVYdFRodW1iOjpJbWFnZTo6aGVpZ2h0ADE5Mg8AcoUAAAAXdEVYdFRodW1iOjpJbWFnZTo6V2lkdGgAMTky06whCAAAABl0RVh0VGh1bWI6Ok1pbWV0eXBlAGltYWdlL3BuZz+yVk4AAAAXdEVYdFRodW1iOjpNVGltZQAxNTQ1MDA3ODE1F0XONAAAAA90RVh0VGh1bWI6OlNpemUAMEJClKI+7AAAAFZ0RVh0VGh1bWI6OlVSSQBmaWxlOi8vL21udGxvZy9mYXZpY29ucy8yMDE4LTEyLTE3LzdkNzRlMWFiNDYzMGU0MDYwNjEyODlkZjkwZGNmYTY5Lmljby5wbmdF4iPwAAAAEnRFWHR4bXBNTTpEZXJpdmVkRnJvbQCXqCQIAAAAAElFTkSuQmCC",
        ];

        $response = $this->json('POST', $this->path, $properties);

        $response->assertStatus(201);
        /** @var Asset $asset */
        $asset = Asset::find($response->json()['id']);
        Storage::disk('public')->assertExists(str_replace(config('app.asset_url'), '', $asset->url));

        $this->assertEquals('An Asset', $asset->name);
        $this->assertEquals($asset->owner_id, $this->organization->id);
        $this->assertEquals($asset->owner_type, 'organization');
    }

    public function testCreateFailsRequiredFieldsMissing()
    {
        $this->actAs(Role::APP_USER);
        factory(OrganizationManager::class)->create([
            'role_id' => Role::MANAGER,
            'organization_id' => $this->organization->id,
            'user_id' => $this->actingAs->id,
        ]);

        $response = $this->json('POST', $this->path);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'file_contents' => ['The file contents field is required.'],
            ]
        ]);
    }

    public function testCreateFailsInvalidStringField()
    {
        $this->actAs(Role::APP_USER);
        factory(OrganizationManager::class)->create([
            'role_id' => Role::MANAGER,
            'organization_id' => $this->organization->id,
            'user_id' => $this->actingAs->id,
        ]);

        $response = $this->json('POST', $this->path, [
            'file_contents' => 324,
            'name' => 35,
            'caption' => 35,
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'file_contents' => ['The file contents must be a string.'],
                'name' => ['The name must be a string.'],
                'caption' => ['The caption must be a string.'],
            ]
        ]);
    }

    public function testCreateFailsInvalidFileType()
    {
        $this->actAs(Role::APP_USER);
        factory(OrganizationManager::class)->create([
            'role_id' => Role::MANAGER,
            'organization_id' => $this->organization->id,
            'user_id' => $this->actingAs->id,
        ]);

        $properties = [
            'file_contents' => 'dGV4dA=='
        ];

        $response = $this->json('POST', $this->path, $properties);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'mime_type' => ["The selected mime type is invalid."]
            ]
        ]);
    }
}