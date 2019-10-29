<?php
declare(strict_types=1);

namespace Tests\Integration\Policies;

use App\Models\User\User;
use App\Policies\ResourcePolicy;
use Tests\TestCase;

/**
 * Class ResourcePolicyTest
 * @package Tests\Integration\Policies
 */
class ResourcePolicyTest extends TestCase
{
    public function testAll()
    {
        $policy = new ResourcePolicy();

        $this->assertTrue($policy->all(new User()));
    }
}