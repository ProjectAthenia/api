<?php
declare(strict_types=1);

namespace App\Http\V1\Controllers\Organization;

use App\Contracts\Repositories\Organization\OrganizationManagerRepositoryContract;
use App\Contracts\Repositories\User\UserRepositoryContract;
use App\Events\Organization\OrganizationManagerCreatedEvent;
use App\Http\V1\Controllers\BaseControllerAbstract;
use App\Http\V1\Controllers\Traits\HasIndexRequests;
use App\Http\V1\Requests;
use App\Models\BaseModelAbstract;
use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationManager;
use App\Traits\CanGetAndUnset;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

/**
 * Class OrganizationController
 * @package App\Http\V1\Controllers\Organization
 */
class OrganizationManagerController extends BaseControllerAbstract
{
    use HasIndexRequests, CanGetAndUnset;

    /**
     * @var OrganizationManagerRepositoryContract
     */
    private $repository;

    /**
     * @var UserRepositoryContract
     */
    private $userRepository;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * OrganizationController constructor.
     * @param OrganizationManagerRepositoryContract $repository
     * @param UserRepositoryContract $userRepository
     * @param Dispatcher $dispatcher
     */
    public function __construct(OrganizationManagerRepositoryContract $repository,
                                UserRepositoryContract $userRepository,
                                Dispatcher $dispatcher)
    {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Requests\Organization\OrganizationManager\IndexRequest $request
     * @param Organization $organization
     * @return LengthAwarePaginator
     */
    public function index(Requests\Organization\OrganizationManager\IndexRequest $request, Organization $organization)
    {
        return $this->repository->findAll($this->filter($request), $this->search($request), $this->order($request), $this->expand($request), $this->limit($request), [$organization], (int)$request->input('page', 1));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Requests\Organization\OrganizationManager\StoreRequest $request
     * @param Organization $organization
     * @return OrganizationManager
     */
    public function store(Requests\Organization\OrganizationManager\StoreRequest $request, Organization $organization)
    {
        $data = $request->json()->all();

        $email = $this->getAndUnset($data, 'email');
        $user = $this->userRepository->findByEmail($email);
        $tempPassword = null;

        if (!$user) {
            $tempPassword = Str::random(12);
            $user = $this->userRepository->create([
                'email' => $email,
                'password' => $tempPassword,
            ]);
        }

        $data['user_id'] = $user->id;

        /** @var OrganizationManager $model */
        $model = $this->repository->create($data, $organization);

        $this->dispatcher->dispatch(new OrganizationManagerCreatedEvent($model, $tempPassword));
        return response($model, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Requests\Organization\OrganizationManager\UpdateRequest $request
     * @param Organization $organization
     * @param OrganizationManager $model
     * @return BaseModelAbstract
     */
    public function update(Requests\Organization\OrganizationManager\UpdateRequest $request, Organization $organization, OrganizationManager $model)
    {
        return $this->repository->update($model, $request->json()->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Requests\Organization\OrganizationManager\DeleteRequest $request
     * @param Organization $organization
     * @param OrganizationManager $model
     * @return null
     */
    public function destroy(Requests\Organization\OrganizationManager\DeleteRequest $request, Organization $organization, OrganizationManager $model)
    {
        $this->repository->delete($model);
        return response(null, 204);
    }
}