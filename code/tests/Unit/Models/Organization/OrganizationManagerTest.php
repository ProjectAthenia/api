<?php
declare(strict_types=1);

namespace Tests\Unit\Models\Organization;

use App\Models\Organization\OrganizationManager;
use Tests\TestCase;

/**
 * Class OrganizationManagerTest
 * @package Tests\Unit\Models\Organization
 */
class OrganizationManagerTest extends TestCase
{
    public function testOrganization()
    {
        $message = new OrganizationManager();
        $relation = $message->organization();

        $this->assertEquals('organizations.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('organization_managers.organization_id', $relation->getQualifiedForeignKeyName());
    }

    public function testRole()
    {
        $message = new OrganizationManager();
        $relation = $message->role();

        $this->assertEquals('roles.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('organization_managers.role_id', $relation->getQualifiedForeignKeyName());
    }

    public function testUser()
    {
        $message = new OrganizationManager();
        $relation = $message->user();

        $this->assertEquals('users.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('organization_managers.user_id', $relation->getQualifiedForeignKeyName());
    }
}