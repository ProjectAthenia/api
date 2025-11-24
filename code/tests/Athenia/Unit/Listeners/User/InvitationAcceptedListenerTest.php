<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Listeners\User;

use App\Athenia\Contracts\Repositories\User\InvitationTokenRepositoryContract;
use App\Athenia\Events\User\InvitationAcceptedEvent;
use App\Athenia\Listeners\User\InvitationAcceptedListener;
use App\Models\Role;
use App\Models\User\InvitationToken;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Tests\CustomMockInterface;
use Tests\TestCase;

/**
 * Class InvitationAcceptedListenerTest
 * @package Tests\Athenia\Unit\Listeners\User
 */
final class InvitationAcceptedListenerTest extends TestCase
{
    public function testHandleWithRole(): void
    {
        /** @var InvitationTokenRepositoryContract|CustomMockInterface $repository */
        $repository = mock(InvitationTokenRepositoryContract::class);

        $listener = new InvitationAcceptedListener($repository);

        $user = new User();
        $user->id = 1;

        $invitationToken = new InvitationToken();
        $invitationToken->id = 1;
        $invitationToken->token = 'test-token';
        $invitationToken->role_id = Role::ARTICLE_EDITOR;

        $event = new InvitationAcceptedEvent($user, $invitationToken);

        // Mock the repository update call
        $repository->shouldReceive('update')->once()->with($invitationToken, \Mockery::on(function ($data) {
            $this->assertArrayHasKey('used_at', $data);
            $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $data['used_at']);
            return true;
        }));

        // Mock the roles relationship
        $rolesRelation = mock(BelongsToMany::class);
        $rolesRelation->shouldReceive('attach')->once()->with(Role::ARTICLE_EDITOR);
        $user->setRelation('roles', $rolesRelation);

        // We need to mock the roles() method
        $user = \Mockery::mock($user)->makePartial();
        $user->shouldReceive('roles')->once()->andReturn($rolesRelation);

        // Update the event with the mocked user
        $event = new InvitationAcceptedEvent($user, $invitationToken);

        $listener->handle($event);
    }

    public function testHandleWithoutRole(): void
    {
        /** @var InvitationTokenRepositoryContract|CustomMockInterface $repository */
        $repository = mock(InvitationTokenRepositoryContract::class);

        $listener = new InvitationAcceptedListener($repository);

        $user = new User();
        $user->id = 1;

        $invitationToken = new InvitationToken();
        $invitationToken->id = 1;
        $invitationToken->token = 'test-token';
        $invitationToken->role_id = null;

        $event = new InvitationAcceptedEvent($user, $invitationToken);

        // Mock the repository update call
        $repository->shouldReceive('update')->once()->with($invitationToken, \Mockery::on(function ($data) {
            $this->assertArrayHasKey('used_at', $data);
            $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $data['used_at']);
            return true;
        }));

        // Mock the roles relationship - it should NOT be called
        $rolesRelation = mock(BelongsToMany::class);
        $rolesRelation->shouldReceive('attach')->never();

        $user = \Mockery::mock($user)->makePartial();
        $user->shouldReceive('roles')->never();

        // Update the event with the mocked user
        $event = new InvitationAcceptedEvent($user, $invitationToken);

        $listener->handle($event);
    }
}
