<?php
declare(strict_types=1);

namespace Tests\Unit\Listeners\Organization;

use App\Contracts\Repositories\Messaging\MessageRepositoryContract;
use App\Events\Organization\OrganizationManagerCreatedEvent;
use App\Listeners\Organization\OrganizationManagerCreatedListener;
use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationManager;
use App\Models\Role;
use App\Models\User\User;
use Tests\TestCase;

/**
 * Class OrganizationManagerCreatedListenerTest
 * @package Tests\Unit\Listeners\Organization
 */
final class OrganizationManagerCreatedListenerTest extends TestCase
{
    public function testHandle(): void
    {
        $organizationManager = new OrganizationManager([
            'organization' => new Organization([
                'name' => 'An Organization',
            ]),
            'role' => new Role([
                'name' => 'A Role',
            ]),
            'user' => new User([
                'name' => 'A Person',
            ]),
        ]);

        $event = new OrganizationManagerCreatedEvent($organizationManager, 'password');

        $repository = mock(MessageRepositoryContract::class);
        $repository->shouldReceive('sendEmailToUser')->once();

        $listener = new OrganizationManagerCreatedListener($repository);
        $listener->handle($event);
    }
}