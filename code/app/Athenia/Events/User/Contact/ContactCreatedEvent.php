<?php
declare(strict_types=1);

namespace App\Athenia\Events\User\Contact;

use App\Models\User\Contact;

/**
 * Class ContactCreatedEvent
 * @package App\Events\User\Contact
 */
class ContactCreatedEvent
{
    /**
     * @var Contact
     */
    private $contact;

    /**
     * ContactCreatedEvent constructor.
     * @param Contact $contact
     */
    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return Contact
     */
    public function getContact(): Contact
    {
        return $this->contact;
    }
}