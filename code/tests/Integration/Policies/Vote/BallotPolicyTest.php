<?php
declare(strict_types=1);

namespace Tests\Integration\Policies\Vote;

use App\Models\User\User;
use App\Models\Vote\Ballot;
use App\Policies\Vote\BallotPolicy;
use Tests\TestCase;

/**
 * Class BallotPolicyTest
 * @package Tests\Integration\Policies\Vote
 */
class BallotPolicyTest extends TestCase
{
    public function testView(): void
    {
        $policy = new BallotPolicy();

        $this->assertTrue($policy->view(new User(), new Ballot()));
    }
}
