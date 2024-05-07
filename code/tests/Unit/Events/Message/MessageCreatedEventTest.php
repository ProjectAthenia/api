<?php
declare(strict_types=1);

namespace Tests\Unit\Events\Message;

use App\Events\Messaging\MessageCreatedEvent;
use App\Models\Messaging\Message;
use Tests\TestCase;

/**
 * Class MessageCreatedEventTest
 * @package Tests\Unit\Events\Message
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