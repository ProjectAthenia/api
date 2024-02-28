<?php
declare(strict_types=1);

namespace Tests\Unit\Models\Organization;

use App\Models\Organization\Organization;
use Tests\TestCase;

/**
 * Class OrganizationTest
 * @package Tests\Unit\Models\Organization
 */
class OrganizationTest extends TestCase
{
    public function testAssets(): void
    {
        $user = new Organization();
        $relation = $user->assets();

        $this->assertEquals('organizations.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('assets.owner_id', $relation->getQualifiedForeignKeyName());
    }

    public function testOrganizationManagers(): void
    {
        $user = new Organization();
        $relation = $user->organizationManagers();

        $this->assertEquals('organizations.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('organization_managers.organization_id', $relation->getQualifiedForeignKeyName());
    }

    public function testPayments(): void
    {
        $user = new Organization();
        $relation = $user->payments();

        $this->assertEquals('organizations.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('payments.owner_id', $relation->getQualifiedForeignKeyName());
    }

    public function testPaymentMethods(): void
    {
        $user = new Organization();
        $relation = $user->paymentMethods();

        $this->assertEquals('organizations.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('payment_methods.owner_id', $relation->getQualifiedForeignKeyName());
    }

    public function testProfileImage(): void
    {
        $model = new Organization();

        $relation = $model->profileImage();

        $this->assertEquals('organizations.profile_image_id', $relation->getQualifiedForeignKeyName());
        $this->assertEquals('assets.id', $relation->getQualifiedOwnerKeyName());
    }

    public function testSubscriptions(): void
    {
        $user = new Organization();
        $relation = $user->subscriptions();

        $this->assertEquals('organizations.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('subscriptions.subscriber_id', $relation->getQualifiedForeignKeyName());
    }
}