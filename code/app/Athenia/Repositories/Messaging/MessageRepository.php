<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\Messaging;

use App\Athenia\Contracts\Models\CanReceiveTextMessagesContract;
use App\Athenia\Contracts\Repositories\Messaging\MessageRepositoryContract;
use App\Athenia\Contracts\Repositories\User\UserRepositoryContract;
use App\Athenia\Models\BaseModelAbstract;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use App\Models\Messaging\Message;
use App\Models\User\User;
use App\Repositories\Traits\NotImplemented as NotImplemented;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class MessageRepository
 * @package App\Repositories\User
 */
class MessageRepository extends BaseRepositoryAbstract implements MessageRepositoryContract
{
    use \App\Athenia\Repositories\Traits\NotImplemented\Delete;

    /**
     * @var UserRepositoryContract
     */
    private UserRepositoryContract $userRepository;

    /**
     * MessageRepository constructor.
     * @param Message $model
     * @param LogContract $log
     * @param UserRepositoryContract $userRepository
     */
    public function __construct(Message $model, LogContract $log, UserRepositoryContract $userRepository)
    {
        parent::__construct($model, $log);
        $this->userRepository = $userRepository;
    }

    /**
     * Overrides to make sure to use the related model for the to field
     *
     * @param array $data
     * @param User|BaseModelAbstract|null $relatedModel
     * @param array $forcedValues
     * @return BaseModelAbstract
     */
    public function create(array $data = [], BaseModelAbstract $relatedModel = null, array $forcedValues = [])
    {
        if ($relatedModel) {
            $data['to_id'] = $relatedModel->id;
        }

        return parent::create($data, null, $forcedValues);
    }

    /**
     * Sends an email directly to a user
     *
     * @param string $email
     * @param string $subject
     * @param string $template
     * @param string $greeting
     * @param array $baseTemplateData
     * @return Message|BaseModelAbstract
     */
    public function sendDirectEmail(string $email, string $subject, string $template, string $greeting, array $baseTemplateData = []): Message
    {
        return $this->create([
            'subject' => $subject,
            'template' => $template,
            'email' => $email,
            'data' => array_merge($baseTemplateData, [
                'greeting' => $greeting,
            ]),
        ]);
    }

    /**
     * Find all
     *
     * @param array $filters
     * @param array $searches
     * @param array $orderBy
     * @param array $with
     * @param int|null $limit pass null to get all
     * @param array $belongsToArray array of models this should belong to
     * @param int $pageNumber
     * @return LengthAwarePaginator|Collection
     */
    public function findAll(array $filters = [], array $searches = [], array $orderBy = [], array $with = [], $limit = 10, array $belongsToArray = [], int $pageNumber = 1)
    {
        $query = $this->buildFindAllQuery($filters, $searches, $orderBy, $with, $belongsToArray);

        $query->orderBy('created_at', 'desc');

        if ($limit) {
            return $query->paginate($limit, $columns = ['*'], $pageName = 'page', $pageNumber);
        }
        return $query->get();
    }

    /**
     * Sends an email directly to a user
     *
     * @param User $user
     * @param string $subject
     * @param string $template
     * @param array $baseTemplateData
     * @param null $greeting
     * @return Message|BaseModelAbstract
     */
    public function sendEmailToUser(
        User $user,
        string $subject,
        string $template,
        array $baseTemplateData = [],
        string $greeting = null,
        array $via = [Message::VIA_EMAIL],
    ): Message {
        return $this->create([
            'subject' => $subject,
            'template' => $template,
            'email' => $user->email,
            'via' => $via,
            'data' => array_merge($baseTemplateData, [
                'greeting' => $greeting ?? 'Hello ' . $user->first_name,
            ]),
        ], $user);
    }

    /**
     * Sends an email directly to the main system users in the system
     *
     * @param string $subject
     * @param string $template
     * @param array $baseTemplateData
     * @param string|null $greeting
     * @return Collection
     */
    public function sendEmailToSuperAdmins(
        string $subject,
        string $template,
        array $baseTemplateData = [],
        string $greeting = null,
        array $via = [Message::VIA_EMAIL],
    ): Collection {
        $messages = new Collection();

        foreach ($this->userRepository->findSuperAdmins() as $user) {
            $messages->push(
                $this->sendEmailToUser($user, $subject, $template, $baseTemplateData, $greeting, $via)
            );
        }

        return $messages;
    }

    /**
     * Sends a text message to a related model
     *
     * @param CanReceiveTextMessagesContract $model
     * @param string $message
     * @return BaseModelAbstract|Message
     */
    public function sendTextMessage(CanReceiveTextMessagesContract $model, string $message): Message
    {
        return $this->create([
            'to_id' => $model->id,
            'to_type' => $model->morphRelationName(),
            'via' => [Message::VIA_SMS],
            'data' => [
                'message' => $message,
            ],
        ]);
    }
}
