<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Listeners\User;

use App\Athenia\Contracts\Repositories\Messaging\MessageRepositoryContract;
use App\Athenia\Events\User\SignUpEvent;
use App\Listeners\User\SignUpListener;
use App\Models\User\User;
use Tests\CustomMockInterface;
use Tests\TestCase;

/**
 * Class SignUpListenerTest
 * @package Tests\Athenia\Unit\Listeners\User
 */
final class SignUpListenerTest extends TestCase
{
    public function testHandle(): void
    {
        /** @var MessageRepositoryContract|CustomMockInterface $messageRepository */
        $repository = mock(MessageRepositoryContract::class);

        $user = new User([
            'first_name' => 'Ralph Nadar',
            'email' => 'test@test.com',
        ]);

        $repository->shouldReceive('sendEmailToUser')->once()->with(
            $user,
            'Welcome to Project Athenia!',
            'sign-up',
            [],
            'Ralph Nadar,',
        );

        $listener = new SignUpListener($repository);

        $event = new SignUpEvent($user);

        $listener->handle($event);
    }
}