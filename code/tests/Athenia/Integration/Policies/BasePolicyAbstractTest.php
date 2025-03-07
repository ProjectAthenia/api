<?php
declare(strict_types=1);

namespace Tests\Athenia\Integration\Policies;

use App\Models\Role;
use Tests\DatabaseSetupTrait;
use Tests\Mocks\BasePolicy;
use Tests\TestCase;
use Tests\Traits\RolesTesting;

/**
 * Class BasePolicyAbstractTest
 * @package Tests\Athenia\Integration\Policies
 */
final class BasePolicyAbstractTest extends TestCase
{
    use RolesTesting, DatabaseSetupTrait;

    public function testBefore(): void
    {
        $policy = new BasePolicy();

        $this->assertNull($policy->before($this->getUserOfRole(Role::APP_USER)));

        $this->assertTrue($policy->before($this->getUserOfRole(Role::SUPER_ADMIN)));
    }
}