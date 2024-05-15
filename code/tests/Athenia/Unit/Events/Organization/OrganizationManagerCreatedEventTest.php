<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Events\Organization;

use App\Athenia\Events\Organization\OrganizationManagerCreatedEvent;
use App\Models\Organization\OrganizationManager;
use Tests\TestCase;

/**
 * Class OrganizationManagerCreatedEventTest
 * @package Tests\Athenia\Unit\Events\Organization
 */
final class OrganizationManagerCreatedEventTest extends TestCase
{
    public function testWithoutPassword(): void
    {
        $organizationManager = new OrganizationManager();
        $event = new OrganizationManagerCreatedEvent($organizationManager);

        $this->assertEquals($organizationManager, $event->getOrganizationManager());
        $this->assertNull($event->getTempPassword());
    }

    public function testWithPassword(): void
    {
        $organizationManager = new OrganizationManager();
        $event = new OrganizationManagerCreatedEvent($organizationManager, 'password');

        $this->assertEquals($organizationManager, $event->getOrganizationManager());
        $this->assertEquals('password', $event->getTempPassword());
    }
}