<?php
declare(strict_types=1);

namespace App\Mail;

use App\Events\Messaging\MessageSentEvent;
use App\Models\Messaging\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class NotificationMailer
 * @package App\Mailers
 */
class MessageMailer extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var Message
     */
    private $message;

    /**
     * NotificationMailer constructor.
     * @param Message $message
     * // TODO revamp this to take in a contract for the entity that can receive emails
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
        $this->chain([new MessageSentEvent($message)]);
    }

    /**
     * Builds the email
     *
     * @return $this
     */
    public function build()
    {
        $name = $this->message->to ? $this->message->to->first_name : null;
        if ($this->message->to && $this->message->to->last_name) {
            $name.= ' ' . $this->message->to->last_name;
        }
        $message = $this->subject($this->message->subject)
            ->to($this->message->email, $name)
            ->from('thehaeckelsociety@gmail.com', 'Project Athenia')
            ->bcc('thehaeckelsociety@gmail.com', 'Project Athenia')
            ->view('mailers.' . $this->message->template, $this->message->data);

        if ($this->message->reply_to_email) {
            $message->replyTo($this->message->reply_to_email, $this->message->reply_to_name);
        }

        if (isset ($this->message->data['attachments'])) {
            foreach ($this->message->data['attachments'] as $attachment) {
                $message->attach($attachment);
            }
        }

        return $message;
    }
}
