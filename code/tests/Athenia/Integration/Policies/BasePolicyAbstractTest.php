<?php
declare(strict_types=1);

namespace Tests\Integration\Policies;

use App\Athenia\Policies\BasePolicyAbstract;
use App\Models\Role;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\RolesTesting;

/**
 * Class BasePolicyAbstractTest
 * @package Tests\Integration\Policies
 */
final class BasePolicyAbstractTest extends TestCase
{
    use RolesTesting, DatabaseSetupTrait;

    public function testBefore(): void
    {
        /** @var BasePolicyAbstract $policy */
        $policy = $this->getMockForAbstractClass(BasePolicyAbstract::class);

        $this->assertNull($policy->before($this->getUserOfRole(Role::APP_USER)));

        $this->assertTrue($policy->before($this->getUserOfRole(Role::SUPER_ADMIN)));
    }
}