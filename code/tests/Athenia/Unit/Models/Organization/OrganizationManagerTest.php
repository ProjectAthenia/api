<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Models\Organization;

use App\Models\Organization\OrganizationManager;
use Tests\TestCase;

/**
 * Class OrganizationManagerTest
 * @package Tests\Athenia\Unit\Models\Organization
 */
final class OrganizationManagerTest extends TestCase
{
    public function testOrganization(): void
    {
        $message = new OrganizationManager();
        $relation = $message->organization();

        $this->assertEquals('organizations.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('organization_managers.organization_id', $relation->getQualifiedForeignKeyName());
    }

    public function testRole(): void
    {
        $message = new OrganizationManager();
        $relation = $message->role();

        $this->assertEquals('roles.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('organization_managers.role_id', $relation->getQualifiedForeignKeyName());
    }

    public function testUser(): void
    {
        $message = new OrganizationManager();
        $relation = $message->user();

        $this->assertEquals('users.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('organization_managers.user_id', $relation->getQualifiedForeignKeyName());
    }
}