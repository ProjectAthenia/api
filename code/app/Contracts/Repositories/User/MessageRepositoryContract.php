<?php
declare(strict_types=1);

namespace App\Contracts\Repositories\User;

use App\Contracts\Models\CanReceiveTextMessagesContract;
use App\Contracts\Repositories\BaseRepositoryContract;
use App\Models\User\Message;
use App\Models\User\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Interface MessageRepositoryContract
 * @package App\Contracts\Repositories\User
 */
interface MessageRepositoryContract extends BaseRepositoryContract
{
    /**
     * Sends an email directly to a user
     *
     * @param User $user
     * @param string $subject
     * @param string $template
     * @param array $baseTemplateData
     * @param string|null $greeting
     * @param array $via
     * @return Message
     */
    public function sendEmailToUser(
        User $user,
        string $subject,
        string $template,
        array $baseTemplateData = [],
        string $greeting = null,
        array $via = [Message::VIA_EMAIL],
    ): Message;

    /**
     * Sends an email directly to the main system users in the system
     *
     * @param string $subject
     * @param string $template
     * @param array $baseTemplateData
     * @param string|null $greeting
     * @param array $via
     * @return Collection
     */
    public function sendEmailToSuperAdmins(
        string $subject,
        string $template,
        array $baseTemplateData = [],
        string $greeting = null,
        array $via = [Message::VIA_EMAIL],
    ): Collection;

    /**
     * Sends an email directly to the passed in email without linking to a model
     *
     * @param string $email
     * @param string $subject
     * @param string $template
     * @param array $baseTemplateData
     * @param string|null $greeting
     * @return Message
     */
    public function sendDirectEmail(string $email, string $subject, string $template, string $greeting, array $baseTemplateData = []): Message;

    /**
     * Sends a text message to a related model
     *
     * @param CanReceiveTextMessagesContract $model
     * @param string $message
     * @return Message
     */
    public function sendTextMessage(CanReceiveTextMessagesContract $model, string $message): Message;
}
