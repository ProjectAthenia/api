<?php
declare(strict_types=1);

namespace App\Contracts\Models\Messaging;

interface CanReceiveEmailsContract extends CanReceiveMessageContract
{
    /**
     * The email address to send the email to
     *
     * @return string
     */
    public function getEmailAddress(): string;

    /**
     * The name of the person to be added as the to field
     *
     * @return string
     */
    public function getEmailToName(): string;
}