<?php
declare(strict_types=1);

namespace App\Contracts\Repositories\User;

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
}
