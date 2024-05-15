<?php
declare(strict_types=1);

namespace Tests\Unit\Models\User;

use App\Models\User\Contact;
use Tests\TestCase;

/**
 * Class ContactTest
 * @package Tests\Unit\Models\User
 */
final class ContactTest extends TestCase
{
    public function testInitiatedBy(): void
    {
        $contact = new Contact();
        $relation = $contact->initiatedBy();

        $this->assertEquals('contacts.initiated_by_id', $relation->getQualifiedForeignKeyName());
        $this->assertEquals('users.id', $relation->getQualifiedOwnerKeyName());
    }

    public function testRequested(): void
    {
        $contact = new Contact();
        $relation = $contact->requested();

        $this->assertEquals('contacts.requested_id', $relation->getQualifiedForeignKeyName());
        $this->assertEquals('users.id', $relation->getQualifiedOwnerKeyName());
    }
}