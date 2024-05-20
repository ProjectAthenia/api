<?php
declare(strict_types=1);

namespace Athenia\Unit\Services\Messaging;

use App\Athenia\Services\Messaging\SendEmailService;
use App\Models\Messaging\Message;
use App\Models\Organization\Organization;
use App\Models\User\User;
use Illuminate\Contracts\Mail\Mailer;
use Tests\TestCase;

class SendEmailServiceTest extends TestCase
{
    public function testSendMessageWithoutEmailReceiver()
    {
        $service = new SendEmailService(mock(Mailer::class));

        $service->sendMessage(new Organization(), new Message());
    }

    public function testSendMessageWithEmailReceiver()
    {
        $mailer = mock(Mailer::class);

        $service = new SendEmailService($mailer);

        $mailer->shouldReceive('send');

        $user = new User();

        $service->sendMessage($user, new Message());
    }
}