<?php
declare(strict_types=1);

namespace Tests\Unit\Listeners\User;

use App\Contracts\Repositories\User\MessageRepositoryContract;
use App\Events\User\ForgotPasswordEvent;
use App\Listeners\User\ForgotPasswordListener;
use App\Models\User\PasswordToken;
use App\Models\User\User;
use Tests\CustomMockInterface;
use Tests\TestCase;

/**
 * Class ForgotPasswordListenerTest
 * @package Tests\Unit\Listeners\User
 */
class ForgotPasswordListenerTest extends TestCase
{
    public function testHandle()
    {
        /** @var MessageRepositoryContract|CustomMockInterface $repository */
        $repository = mock(MessageRepositoryContract::class);

        $listener = new ForgotPasswordListener($repository);

        $passwordToken = new PasswordToken([
            'user' => new User(),
            'token' => 'hello'
        ]);

        $event = new ForgotPasswordEvent($passwordToken);

        $repository->shouldReceive('create')->once()->with(\Mockery::on(function ($data) {
            $this->assertArrayHasKey('subject', $data);
            $this->assertArrayHasKey('email', $data);
            $this->assertArrayHasKey('template', $data);
            $this->assertEquals('forgot-password', $data['template']);

            $this->assertArrayHasKey('data', $data);
            $this->assertArrayHasKey('greeting', $data['data']);
            $this->assertArrayHasKey('token', $data['data']);
            $this->assertEquals('hello', $data['data']['token']);

            return true;
        }), $passwordToken->user);

        $listener->handle($event);
    }
}