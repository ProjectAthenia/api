<?php
declare(strict_types=1);

namespace Tests\Athenia\Integration\Policies\Vote;

use App\Models\User\User;
use App\Models\Vote\Ballot;
use App\Policies\Vote\BallotPolicy;
use Tests\TestCase;

/**
 * Class BallotPolicyTest
 * @package Tests\Athenia\Integration\Policies\Vote
 */
final class BallotPolicyTest extends TestCase
{
    public function testView(): void
    {
        $policy = new BallotPolicy();

        $this->assertTrue($policy->view(new User(), new Ballot()));
    }
}
