<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Controllers\User;

use App\Athenia\Contracts\Repositories\User\ContactRepositoryContract;
use App\Athenia\Events\User\Contact\ContactCreatedEvent;
use App\Athenia\Http\Core\Controllers\BaseControllerAbstract;
use App\Athenia\Http\Core\Controllers\Traits\HasIndexRequests;
use App\Athenia\Models\BaseModelAbstract;
use App\Http\Core\Requests;
use App\Models\User\Contact;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

/**
 * Class ContactControllerAbstract
 * @package App\Http\Core\Controllers\Userzz
 */
abstract class ContactControllerAbstract extends BaseControllerAbstract
{
    use HasIndexRequests;

    /**
     * @var ContactRepositoryContract
     */
    private $repository;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * ContactController constructor.
     * @param ContactRepositoryContract $repository
     * @param Dispatcher $dispatcher
     */
    public function __construct(ContactRepositoryContract $repository, Dispatcher $dispatcher)
    {
        $this->repository = $repository;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Requests\User\Contact\IndexRequest $request
     * @param User $user
     * @return LengthAwarePaginator
     */
    public function index(Requests\User\Contact\IndexRequest $request, User $user)
    {
        return $this->repository->findAll($this->filter($request), $this->search($request), $this->order($request), $this->expand($request), $this->limit($request), [$user], (int)$request->input('page', 1));
    }

    /**
     * @param Requests\User\Contact\StoreRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function store(Requests\User\Contact\StoreRequest $request, User $user)
    {
        $data = $request->json()->all();

        $data['initiated_by_id'] = $user->id;

        /** @var Contact $model */
        $model = $this->repository->create($data);

        $this->dispatcher->dispatch(new ContactCreatedEvent($model));

        return new JsonResponse($model, 201);
    }

    /**
     * Updates an event participant, mostly used to link assets
     *
     * @param Requests\User\Contact\UpdateRequest $request
     * @param User $user
     * @param Contact $contact
     * @return BaseModelAbstract
     * @throws \Exception
     */
    public function update(Requests\User\Contact\UpdateRequest $request, User $user, Contact $contact)
    {
        $requestData = $request->json()->all();

        $data = [];

        if (isset($requestData['confirm'])) {
            $data['confirmed_at'] = new Carbon();
        }
        if (isset($requestData['deny'])) {
            $data['denied_at'] = new Carbon();
        }

        return $this->repository->update($contact, $data);
    }
}