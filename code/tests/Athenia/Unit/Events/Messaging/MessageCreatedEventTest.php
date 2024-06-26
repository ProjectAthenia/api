<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Events\Messaging;

use App\Athenia\Events\Messaging\MessageCreatedEvent;
use App\Models\Messaging\Message;
use Tests\TestCase;

/**
 * Class MessageCreatedEventTest
 * @package Tests\Athenia\Unit\Events\Message
 */
final class MessageCreatedEventTest extends TestCase
{
    public function testGetMessage(): void
    {
        $message = new Message();

        $event = new MessageCreatedEvent($message);
        $this->assertEquals($message, $event->getMessage());
    }
}