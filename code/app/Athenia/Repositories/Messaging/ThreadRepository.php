<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\Messaging;

use App\Athenia\Contracts\Repositories\Messaging\ThreadRepositoryContract;
use App\Athenia\Models\BaseModelAbstract;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use App\Athenia\Traits\CanGetAndUnset;
use App\Models\Messaging\Thread;
use App\Repositories\Traits\NotImplemented;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class ThreadRepository
 * @package App\Repositories\User
 */
class ThreadRepository extends BaseRepositoryAbstract implements ThreadRepositoryContract
{
    use CanGetAndUnset, \App\Athenia\Repositories\Traits\NotImplemented\Update;

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