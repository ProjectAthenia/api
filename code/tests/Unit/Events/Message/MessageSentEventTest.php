<?php
declare(strict_types=1);

namespace Tests\Unit\Events\Message;

use App\Events\Messaging\MessageSentEvent;
use App\Models\Messaging\Message;
use Tests\TestCase;

/**
 * Class MessageSentEventTest
 * @package Tests\Unit\Events\Message
 */
final class MessageSentEventTest extends TestCase
{
    public function testGetMessage(): void
    {
        $message = new Message();

        $event = new MessageSentEvent($message);
        $this->assertEquals($message, $event->getMessage());
    }
}