<?php
declare(strict_types=1);

namespace Tests\Unit\Events\Message;

use App\Events\Message\MessageSentEvent;
use App\Models\User\Message;
use Tests\TestCase;

/**
 * Class MessageSentEventTest
 * @package Tests\Unit\Events\Message
 */
class MessageSentEventTest extends TestCase
{
    public function testGetMessage()
    {
        $message = new Message();

        $event = new MessageSentEvent($message);
        $this->assertEquals($message, $event->getMessage());
    }
}