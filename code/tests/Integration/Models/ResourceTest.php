<?php
declare(strict_types=1);

namespace Tests\Integration\Models;

use App\Models\Resource;
use App\Models\User\User;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class ResourceTest
 * @package Tests\Integration\Models
 */
class ResourceTest extends TestCase
{
    use DatabaseSetupTrait;

    public function testResource()
    {
        User::unsetEventDispatcher();
        $user = User::factory()->create();

        /** @var Resource $resource */
        $resource = Resource::factory()->create([
            'resource_id' => $user->id,
            'resource_type' => 'user',
        ]);

        $this->assertInstanceOf(User::class, $resource->resource);
    }
}
