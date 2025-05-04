<?php
declare(strict_types=1);

namespace Tests\Athenia\Integration\Policies\Statistics;

use App\Models\Role;
use App\Models\User\User;
use App\Policies\Statistics\StatisticPolicy;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\RolesTesting;

/**
 * Class StatisticPolicyTest
 * @package Tests\Athenia\Integration\Policies\Statistics
 */
class StatisticPolicyTest extends TestCase
{
    use RolesTesting, DatabaseSetupTrait;

    public function testAllPasses()
    {
        $policy = new StatisticPolicy();
        $this->assertTrue($policy->all(new User()));
    }

    public function testViewFailsIncorrectRole()
    {
        $policy = new StatisticPolicy();

        foreach ($this->rolesWithoutAdmins([Role::CONTENT_EDITOR, Role::SUPPORT_STAFF]) as $role) {
            $user = $this->getUserOfRole($role);

            $this->assertFalse($policy->view($user));
        }
    }

    public function testViewPassesCorrectRole()
    {
        $policy = new StatisticPolicy();

        foreach ([Role::CONTENT_EDITOR, Role::SUPPORT_STAFF] as $role) {
            $user = $this->getUserOfRole($role);

            $this->assertTrue($policy->view($user));
        }
    }

    public function testCreateFailsIncorrectRole()
    {
        $policy = new StatisticPolicy();

        foreach ($this->rolesWithoutAdmins([Role::CONTENT_EDITOR]) as $role) {
            $user = $this->getUserOfRole($role);

            $this->assertFalse($policy->create($user));
        }
    }

    public function testCreatePassesCorrectRole()
    {
        $policy = new StatisticPolicy();

        $user = $this->getUserOfRole(Role::CONTENT_EDITOR);

        $this->assertTrue($policy->create($user));
    }

    public function testUpdateFailsIncorrectRole()
    {
        $policy = new StatisticPolicy();

        foreach ($this->rolesWithoutAdmins([Role::CONTENT_EDITOR]) as $role) {
            $user = $this->getUserOfRole($role);

            $this->assertFalse($policy->update($user));
        }
    }

    public function testUpdatePassesCorrectRole()
    {
        $policy = new StatisticPolicy();

        $user = $this->getUserOfRole(Role::CONTENT_EDITOR);

        $this->assertTrue($policy->update($user));
    }

    public function testDeleteFailsIncorrectRole()
    {
        $policy = new StatisticPolicy();

        foreach ($this->rolesWithoutAdmins([Role::CONTENT_EDITOR]) as $role) {
            $user = $this->getUserOfRole($role);

            $this->assertFalse($policy->delete($user));
        }
    }

    public function testDeletePassesCorrectRole()
    {
        $policy = new StatisticPolicy();

        $user = $this->getUserOfRole(Role::CONTENT_EDITOR);

        $this->assertTrue($policy->delete($user));
    }
} 