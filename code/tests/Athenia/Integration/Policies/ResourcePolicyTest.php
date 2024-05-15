<?php
declare(strict_types=1);

namespace Tests\Athenia\Integration\Policies;

use App\Models\User\User;
use App\Policies\ResourcePolicy;
use Tests\TestCase;

/**
 * Class ResourcePolicyTest
 * @package Tests\Athenia\Integration\Policies
 */
final class ResourcePolicyTest extends TestCase
{
    public function testAll(): void
    {
        $policy = new ResourcePolicy();

        $this->assertTrue($policy->all(new User()));
    }
}