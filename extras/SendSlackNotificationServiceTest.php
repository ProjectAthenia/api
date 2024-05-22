<?php
declare(strict_types=1);

namespace Athenia\Unit\Services\Messaging;

use App\Athenia\Services\Messaging\SendSlackNotificationService;
use App\Models\Messaging\Message;
use App\Models\Organization\Organization;
use App\Models\User\User;
use JoliCode\Slack\Client;
use Psr\Http\Client\ClientInterface;
use Tests\TestCase;

class ClientFactoryMock
{
    /**
     * @var \Tests\CustomMockInterface|Client
     */
    public static $client;

    public static function create(string $token, ClientInterface $httpClient = null): Client {
        return static::$client;
    }
}
class SendSlackNotificationServiceTest extends TestCase
{
    public function __construct(string $name)
    {
        parent::__construct($name);
        class_alias(ClientFactoryMock::class, \JoliCode\Slack\ClientFactory::class);
    }

    public function testSendMessageDoesNothing()
    {
        $service = new SendSlackNotificationService();

        $result = $service->sendMessage(new User(), new Message([
            'via' => [Message::VIA_SLACK],
        ]));

        $this->assertFalse($result);
    }

    public function testSendMessagePostsMessage()
    {
        $service = new SendSlackNotificationService();

        ClientFactoryMock::$client = mock(Client::class);
        ClientFactoryMock::$client->shouldReceive('chatPostMessage');

        $result = $service->sendMessage(new Organization([
            'slack_key' => 'key',
            'slack_channel' => 'channel',
        ]), new Message([
            'via' => [Message::VIA_SLACK],
        ]));

        $this->assertTrue($result);
    }
}