<?php
declare(strict_types=1);

namespace Athenia\Unit\Services\Messaging;

use App\Athenia\Services\Messaging\SendSMSNotificationService;
use App\Models\Messaging\Message;
use App\Models\Organization\Organization;
use App\Models\User\User;
use NotificationChannels\Twilio\Twilio;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

class SendSMSNotificationServiceTest extends TestCase
{
    use MocksApplicationLog;

    private $twilio;

    private SendSMSNotificationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->twilio = mock(Twilio::class);
        $this->service = new SendSMSNotificationService(
            $this->twilio,
            $this->getGenericLogMock(),
        );
    }

    public function testSendMessageDoesNothing()
    {
        $result = $this->service->sendMessage(new Organization(), new Message([
            'via' => [Message::VIA_SMS],
        ]));
        $this->assertFalse($result);
    }

    public function testSendMessagePostsMessage()
    {
        $this->twilio->shouldReceive('sendMessage');

        $result = $this->service->sendMessage(new User([
            'phone' => '41412344569',
        ]), new Message([
            'data' => [
                'message' => 'Hello',
            ],
            'via' => [Message::VIA_SMS],
        ]));
        $this->assertTrue($result);
    }
}