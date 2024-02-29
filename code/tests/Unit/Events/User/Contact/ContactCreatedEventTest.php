<?php
declare(strict_types=1);

namespace Tests\Unit\Events\User\Contact;

use App\Events\User\Contact\ContactCreatedEvent;
use App\Models\User\Contact;
use Tests\TestCase;

/**
 * Class ContactCreatedEventTest
 * @package Tests\Unit\Events\User\Contact
 */
final class ContactCreatedEventTest extends TestCase
{
    public function testGetContact(): void
    {
        $contact = new Contact();

        $event = new ContactCreatedEvent($contact);

        $this->assertEquals($contact, $event->getContact());
    }
}