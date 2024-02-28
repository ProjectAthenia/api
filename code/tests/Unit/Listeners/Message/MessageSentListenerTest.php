<?php
declare(strict_types=1);

namespace Tests\Unit\Listeners\Message;

use App\Contracts\Repositories\User\MessageRepositoryContract;
use App\Events\Message\MessageSentEvent;
use App\Listeners\Message\MessageSentListener;
use App\Models\User\Message;
use Carbon\Carbon;
use Tests\TestCase;

/**
 * Class MessageSentListenerTest
 * @package Tests\Unit\Listeners\Message
 */
class MessageSentListenerTest extends TestCase
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