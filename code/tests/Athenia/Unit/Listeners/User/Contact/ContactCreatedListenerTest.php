<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Listeners\User\Contact;

use App\Athenia\Contracts\Repositories\Messaging\MessageRepositoryContract;
use App\Athenia\Events\User\Contact\ContactCreatedEvent;
use App\Listeners\User\Contact\ContactCreatedListener;
use App\Models\User\Contact;
use App\Models\User\User;
use Tests\TestCase;

/**
 * Class ContactCreatedListenerTest
 * @package Tests\Athenia\Unit\Listeners\User\Contact
 */
final class ContactCreatedListenerTest extends TestCase
{
    public function testHandle(): void
    {
        $messageRepository = mock(MessageRepositoryContract::class);
        $listener = new ContactCreatedListener($messageRepository);

        $contact = new Contact([
            'initiatedBy' => new User([
                'first_name' => 'Steve',
                'last_name' => 'Brown',
            ]),
        ]);
        $event = new ContactCreatedEvent($contact);

        $messageRepository->shouldReceive('create')->once();

        $listener->handle($event);
    }
}