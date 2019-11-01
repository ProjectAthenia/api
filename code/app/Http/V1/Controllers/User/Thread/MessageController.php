<?php
declare(strict_types=1);

namespace App\Http\V1\Controllers\User\Thread;

use App\Contracts\Repositories\User\MessageRepositoryContract;
use App\Http\V1\Controllers\BaseControllerAbstract;
use App\Http\V1\Controllers\Traits\HasIndexRequests;
use App\Http\V1\Requests;
use App\Models\User\Message;
use App\Models\User\Thread;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

/**
 * Class MessageController
 * @package App\Http\V1\Controllers\User\Thread
 */
class MessageController extends BaseControllerAbstract
{
    use HasIndexRequests;

    /**
     * @var MessageRepositoryContract
     */
    private $repository;

    /**
     * MessageController constructor.
     * @param MessageRepositoryContract $repository
     */
    public function __construct(MessageRepositoryContract $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Requests\User\Thread\Message\IndexRequest $request
     * @param User $user
     * @param Thread $thread
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Requests\User\Thread\Message\IndexRequest $request, User $user, Thread $thread)
    {
        return $this->repository->findAll($this->filter($request), $this->search($request), $this->expand($request), $this->limit($request), [$thread], (int)$request->input('page', 1));
    }

    /**
     * @param Requests\User\Thread\Message\StoreRequest $request
     * @param User $user
     * @param Thread $thread
     * @return JsonResponse
     */
    public function store(Requests\User\Thread\Message\StoreRequest $request, User $user, Thread $thread) : JsonResponse
    {
        $message = $request->json('message');
        $data = [
            'from_id' => $user->id,
            'thread_id' => $thread->id,
            'via' => [Message::VIA_PUSH_NOTIFICATION],
            'data' => [
                'body' => $message,
                'title' => 'New message from ' . $user->first_name,
            ],
            'action' => '/user/' . $user->id . '/message',
        ];

        return new JsonResponse($this->repository->create($data), 201);
    }

    /**
     * Updates a message, mostly used to set the message as seen
     *
     * @param Requests\User\Thread\Message\UpdateRequest $request
     * @param User $user
     * @param Thread $thread
     * @param Message $message
     * @return \App\Models\BaseModelAbstract
     */
    public function update(Requests\User\Thread\Message\UpdateRequest $request, User $user, Thread $thread, Message $message)
    {
        $requestData = $request->json()->all();

        $data = [];

        if (isset($requestData['seen'])) {
            $data['seen_at'] = new Carbon();
        }

        return $this->repository->update($message, $data);
    }
}