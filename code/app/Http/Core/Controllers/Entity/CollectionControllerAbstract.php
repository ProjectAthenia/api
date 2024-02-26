<?php
declare(strict_types=1);

namespace App\Http\Core\Controllers\Entity;

use App\Contracts\Models\IsAnEntity;
use App\Contracts\Repositories\Collection\CollectionRepositoryContract;
use App\Http\Core\Controllers\BaseControllerAbstract;
use App\Http\Core\Controllers\Traits\HasIndexRequests;
use App\Http\Core\Requests;
use App\Models\Role;
use App\Models\User\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class ThreadControllerAbstract
 * @package App\Http\Core\Controllers\User
 */
abstract class CollectionControllerAbstract extends BaseControllerAbstract
{
    use HasIndexRequests;

    /**
     * ThreadController constructor.
     * @param CollectionRepositoryContract $repository
     */
    public function __construct(protected CollectionRepositoryContract $repository)
    {}

    /**
     * @param \App\Http\Core\Requests\Entity\Collection\IndexRequest $request
     * @param User $user
     * @return LengthAwarePaginator
     */
    public function index(Requests\Entity\Collection\IndexRequest $request, IsAnEntity $entity)
    {
        $filter = $this->filter($request);

        $filter[] = [
            'owner_id',
            '=',
            $entity->id,
        ];
        $filter[] = [
            'owner_type',
            '=',
            $entity->morphRelationName(),
        ];

        /** @var User $loggedInUser */
        $loggedInUser = Auth::user();

        if (!$loggedInUser || !$entity->canUserManageEntity($loggedInUser, Role::MANAGER)) {
            $filter[] = [
                'is_public',
                '=',
                '1'
            ];
        }

        return $this->repository->findAll($filter, $this->search($request), $this->order($request), $this->expand($request), $this->limit($request), [], (int)$request->input('page', 1));
    }

    /**
     * @param \App\Http\Core\Requests\Entity\Collection\StoreRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function store(Requests\Entity\Collection\StoreRequest $request, IsAnEntity $entity) : JsonResponse
    {
        $data = $request->json()->all();

        $data['owner_id'] = $entity->id;
        $data['owner_type'] = $entity->morphRelationName();

        return new JsonResponse($this->repository->create($data), 201);
    }
}