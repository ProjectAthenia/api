<?php
declare(strict_types=1);

namespace Tests\Athenia\Integration\Policies;

use App\Models\Feature;
use App\Models\User\User;
use App\Policies\FeaturePolicy;
use Tests\TestCase;

/**
 * Class FeaturePolicyTest
 * @package Tests\Athenia\Integration\Policies
 */
final class FeaturePolicyTest extends TestCase
{
    public function testAll(): void
    {
        $policy = new FeaturePolicy();

        $this->assertFalse($policy->all(new User()));
    }

    public function testView(): void
    {
        $policy = new FeaturePolicy();

        $this->assertFalse($policy->view(new User(), new Feature()));
    }
}
