<?php
declare(strict_types=1);

namespace App\Repositories\Messaging;

use App\Contracts\Repositories\Messaging\ThreadRepositoryContract;
use App\Models\BaseModelAbstract;
use App\Models\Messaging\Thread;
use App\Repositories\BaseRepositoryAbstract;
use App\Repositories\Traits\NotImplemented;
use App\Traits\CanGetAndUnset;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class ThreadRepository
 * @package App\Repositories\User
 */
class ThreadRepository extends BaseRepositoryAbstract implements ThreadRepositoryContract
{
    use CanGetAndUnset, NotImplemented\Update;

    /**
     * ThreadRepository constructor.
     * @param Thread $model
     * @param LogContract $log
     */
    public function __construct(Thread $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }

    /**
     * Links the users properly
     *
     * @param array $data
     * @param BaseModelAbstract|null $relatedModel
     * @param array $forcedValues
     * @return BaseModelAbstract|Thread
     */
    public function create(array $data = [], BaseModelAbstract $relatedModel = null, array $forcedValues = [])
    {
        $users = $this->getAndUnset($data, 'users', []);

        /** @var Thread $thread */
        $thread = parent::create($data, $relatedModel, $forcedValues);

        $thread->users()->sync($users);

        return $thread;
    }
}