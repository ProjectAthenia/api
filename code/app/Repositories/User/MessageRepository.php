<?php
declare(strict_types=1);

namespace App\Repositories\User;

use App\Contracts\Repositories\User\UserRepositoryContract;
use App\Models\BaseModelAbstract;
use App\Models\User\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Psr\Log\LoggerInterface as LogContract;
use App\Contracts\Repositories\User\MessageRepositoryContract;
use App\Models\User\Message;
use App\Repositories\BaseRepositoryAbstract;
use App\Repositories\Traits\NotImplemented as NotImplemented;

/**
 * Class MessageRepository
 * @package App\Repositories\User
 */
class MessageRepository extends BaseRepositoryAbstract implements MessageRepositoryContract
{
    use NotImplemented\Delete, NotImplemented\FindOrFail;

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
     * @param User $user
     * @param string $subject
     * @param string $template
     * @param array $baseTemplateData
     * @param null $greeting
     * @return Message|BaseModelAbstract
     */
    public function sendEmailToUser(User $user, string $subject, string $template, array $baseTemplateData = [], $greeting = null): Message
    {
        return $this->create([
            'subject' => $subject,
            'template' => $template,
            'email' => $user->email,
            'data' => array_merge($baseTemplateData, [
                'greeting' => $greeting ?? 'Hello ' . $user->first_name,
            ]),
        ], $user);
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

        if ($limit) {
            return $query->paginate($limit, $columns = ['*'], $pageName = 'page', $pageNumber);
        }
        return $query->get();
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
    public function sendEmailToSuperAdmins(string $subject, string $template, array $baseTemplateData = [], $greeting = null): Collection
    {
        $messages = new Collection();

        foreach ($this->userRepository->findSuperAdmins() as $user) {
            $messages->push($this->sendEmailToUser($user, $subject, $template, $baseTemplateData, $greeting));
        }

        return $messages;
    }
}
