<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Controllers;

use App\Athenia\Contracts\Repositories\Organization\OrganizationManagerRepositoryContract;
use App\Athenia\Contracts\Repositories\Organization\OrganizationRepositoryContract;
use App\Athenia\Http\Core\Controllers\Traits\HasIndexRequests;
use App\Athenia\Models\BaseModelAbstract;
use App\Http\Core\Requests;
use App\Models\Organization\Organization;
use App\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

/**
 * Class OrganizationControllerAbstract
 * @package App\Http\Core\Controllers
 */
abstract class OrganizationControllerAbstract extends BaseControllerAbstract
{
    use HasIndexRequests;

    /**
     * @var OrganizationRepositoryContract
     */
    protected OrganizationRepositoryContract $repository;

    /**
     * @var OrganizationManagerRepositoryContract
     */
    protected OrganizationManagerRepositoryContract $organizationManagerRepository;

    /**
     * OrganizationController constructor.
     * @param OrganizationRepositoryContract $repository
     * @param OrganizationManagerRepositoryContract $organizationManagerRepository
     */
    public function __construct(OrganizationRepositoryContract $repository,
                                OrganizationManagerRepositoryContract $organizationManagerRepository)
    {
        $this->repository = $repository;
        $this->organizationManagerRepository = $organizationManagerRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Athenia\Http\Core\Requests\Organization\IndexRequest $request
     * @return LengthAwarePaginator
     */
    public function index(\App\Athenia\Http\Core\Requests\Organization\IndexRequest $request)
    {
        return $this->repository->findAll($this->filter($request), $this->search($request), $this->order($request), $this->expand($request), $this->limit($request), [], (int)$request->input('page', 1));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Athenia\Http\Core\Requests\Organization\RetrieveRequest $request
     * @param Organization $model
     * @return Organization
     */
    public function show(\App\Athenia\Http\Core\Requests\Organization\RetrieveRequest $request, Organization $model)
    {
        return $model->load($this->expand($request));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Athenia\Http\Core\Requests\Organization\StoreRequest $request
     * @return Organization
     */
    public function store(\App\Athenia\Http\Core\Requests\Organization\StoreRequest $request)
    {
        $model = $this->repository->create($request->json()->all());
        $this->organizationManagerRepository->create([
            'organization_id' => $model->id,
            'role_id' => Role::ADMINISTRATOR,
            'user_id' => Auth::user()->id,
        ]);
        return response($model, 201)->header('Location', route('v1.organizations.show', ['organization' => $model]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Athenia\Http\Core\Requests\Organization\UpdateRequest $request
     * @param Organization $model
     * @return BaseModelAbstract
     */
    public function update(\App\Athenia\Http\Core\Requests\Organization\UpdateRequest $request, Organization $model)
    {
        return $this->repository->update($model, $request->json()->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Athenia\Http\Core\Requests\Organization\DeleteRequest $request
     * @param Organization $model
     * @return null
     */
    public function destroy(\App\Athenia\Http\Core\Requests\Organization\DeleteRequest $request, Organization $model)
    {
        $this->repository->delete($model);
        return response(null, 204);
    }
}