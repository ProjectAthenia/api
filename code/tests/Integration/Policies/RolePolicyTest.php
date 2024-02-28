<?php
declare(strict_types=1);

namespace Tests\Integration\Policies;

use App\Models\User\User;
use App\Policies\RolePolicy;
use Tests\TestCase;

/**
 * Class RolePolicyTest
 * @package Tests\Integration\Policies
 */
class RolePolicyTest extends TestCase
{
    public function testAll(): void
    {
        $policy = new RolePolicy();

        $this->assertFalse($policy->all(new User()));
    }
}