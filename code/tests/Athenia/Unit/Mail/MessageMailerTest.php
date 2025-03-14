<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Mail;

use App\Athenia\Mail\MessageMailer;
use App\Models\Messaging\Message;
use App\Models\User\User;
use Tests\TestCase;

/**
 * Class MessageMailerTest
 * @package Tests\Athenia\Unit\Mail
 */
final class MessageMailerTest extends TestCase
{
    public function testBuild(): void
    {
        $user = new User([
            'first_name' => 'Darlene',
            'last_name' => 'Dora'
        ]);
        $message = new Message([
            'to' => $user,
            'subject' => 'Test Message',
            'email' => 'darlene@test.com',
            'reply_to_email' => 'john@test.com',
            'template' => 'base',
            'data' => [
                'greeting' => 'Hello Darlene!',
            ],
        ]);

        $messageMailer = new MessageMailer($user, $message);

        $builtMailer = $messageMailer->build();

        $this->assertEquals([['name' => 'Darlene Dora', 'address' => 'darlene@test.com']], $builtMailer->to);
        $this->assertEquals([['name' => 'Example', 'address' => 'hello@example.com']], $builtMailer->from);
        $this->assertEquals([['name' => null, 'address' => 'john@test.com']], $builtMailer->replyTo);

        $this->assertEquals('Test Message', $builtMailer->subject);
        $this->assertEquals('mailers.base', $builtMailer->view);
        $this->assertEquals(['greeting' => 'Hello Darlene!'], $builtMailer->viewData);
    }
}
