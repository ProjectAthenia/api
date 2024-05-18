<?php
declare(strict_types=1);

namespace Athenia\Unit\Services\Messaging;

use App\Athenia\Contracts\Services\Messaging\SendSlackNotificationServiceContract;
use App\Athenia\Services\Messaging\MessageSendingSelectionService;
use App\Models\Messaging\Message;
use Tests\TestCase;

class MessageSendingSelectionServiceTest extends TestCase
{
    public function testGetSendingService()
    {
        $slack = mock(SendSlackNotificationServiceContract::class);

        $service = new MessageSendingSelectionService([
            Message::VIA_SLACK => $slack,
        ]);

        $this->assertNull($service->getSendingService(Message::VIA_EMAIL));
        $this->assertEquals($slack, $service->getSendingService(Message::VIA_SLACK));
    }
}