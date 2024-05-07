<?php
declare(strict_types=1);

namespace Tests\Unit\Listeners\Message;

use App\Contracts\Repositories\Messaging\MessageRepositoryContract;
use App\Events\Messaging\MessageSentEvent;
use App\Listeners\Messaging\MessageSentListener;
use App\Models\Messaging\Message;
use Carbon\Carbon;
use Tests\TestCase;

/**
 * Class MessageSentListenerTest
 * @package Tests\Unit\Listeners\Message
 */
final class MessageSentListenerTest extends TestCase
{
    public function testHandle(): void
    {
        $messageRepository = mock(MessageRepositoryContract::class);
        $listener = new MessageSentListener($messageRepository);

        $message = new Message();
        $event = new MessageSentEvent($message);

        $carbon = new Carbon();
        Carbon::setTestNow($carbon);

        $messageRepository->shouldReceive('update')->once()->with($message, ['sent_at' => $carbon]);

        $listener->handle($event);
    }
}