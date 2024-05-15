<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\User;

use App\Athenia\Contracts\Repositories\User\ContactRepositoryContract;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use App\Models\User\Contact;
use App\Models\User\User;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class ContactRepository
 * @package App\Repositories\User
 */
class ContactRepository extends BaseRepositoryAbstract implements ContactRepositoryContract
{
    /**
     * ContactRepository constructor.
     * @param Contact $model
     * @param LogContract $log
     */
    public function __construct(Contact $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }

    /**
     * @param array $filter
     * @param array $search
     * @param array $orderBy
     * @param array $with
     * @param int $limit
     * @param array $belongsToArray
     * @param int $pageNumber
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     */
    public function findAll(array $filter = [], array $search = [], array $orderBy = [], array $with = [], $limit = 10, array $belongsToArray = [], int $pageNumber = 1)
    {
        $query = parent::buildFindAllQuery($filter, $search, $orderBy, $with, []);

        /** @var User $user */
        foreach ($belongsToArray as $user) {
            $query->where('initiated_by_id', $user->id);
            $query->orWhere('requested_id', $user->id);
        }

        return $query->paginate($limit, $columns = ['*'], $pageName = 'page', $pageNumber);
    }
}