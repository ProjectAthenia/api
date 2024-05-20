<?php
declare(strict_types=1);

namespace Athenia\Unit\Services\Messaging;

use App\Athenia\Services\Messaging\SendPushNotificationService;
use App\Models\Messaging\Message;
use App\Models\Messaging\PushNotificationKey;
use App\Models\Organization\Organization;
use App\Models\User\User;
use GuzzleHttp\Client;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

class SendPushNotificationServiceTest extends TestCase
{
    use MocksApplicationLog;

    private $client;

    private SendPushNotificationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = mock(Client::class);

        $this->service = new SendPushNotificationService(
            '',
            $this->client,
            $this->getGenericLogMock(),
        );
    }

    public function testFormatPushNotification()
    {
        $data = [
            'title' => 'hi',
        ];
        $message = new Message([
            'data' => $data,
            'action' => 'https://app.something.com',
        ]);

        $notification = $this->service->formatPushNotification($message);

        $this->assertStringContainsString(json_encode($data), $notification->formatData());
    }

    public function testSendPushNotification()
    {
        $message = new Message([
            'data' => [
                'title' => 'hi',
            ],
            'action' => 'https://app.something.com',
        ]);

        $notification = $this->service->formatPushNotification($message);

        $pushNotificationKey = new PushNotificationKey([
            'push_notification_key' => 'hello',
        ]);

        $this->client->shouldReceive('post');

        $result = $this->service->sendPushNotification($notification, $pushNotificationKey);

        $this->assertTrue($result);
    }

    public function testSendMessageDoesNothingWithInvalidReceiver()
    {
        $message = new Message([
            'via' => [
                Message::VIA_PUSH_NOTIFICATION,
            ],
            'data' => [
                'title' => 'hi',
            ],
            'action' => 'https://app.something.com',
        ]);

        $result = $this->service->sendMessage(new Organization(), $message);

        $this->assertFalse($result);
    }

    public function testSendMessageSuccessful()
    {
        $user = new User([
            'pushNotificationKeys' => collect([
                new PushNotificationKey([
                    'push_notification_key' => 'hello',
                ]),
                new PushNotificationKey([
                    'push_notification_key' => 'bye',
                ])
            ])
        ]);
        $message = new Message([
            'via' => [
                Message::VIA_PUSH_NOTIFICATION,
            ],
            'data' => [
                'title' => 'hi',
            ],
            'action' => 'https://app.something.com',
        ]);


        $this->client->shouldReceive('post')->times(2);

        $result = $this->service->sendMessage($user, $message);

        $this->assertTrue($result);
    }
}