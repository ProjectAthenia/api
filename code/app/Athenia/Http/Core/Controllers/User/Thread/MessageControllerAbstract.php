<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Controllers\User\Thread;

use App\Athenia\Contracts\Repositories\Messaging\MessageRepositoryContract;
use App\Athenia\Http\Core\Controllers\BaseControllerAbstract;
use App\Athenia\Http\Core\Controllers\Traits\HasIndexRequests;
use App\Athenia\Models\BaseModelAbstract;
use App\Http\Core\Requests;
use App\Models\Messaging\Message;
use App\Models\Messaging\Thread;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

/**
 * Class MessageControllerAbstract
 * @package App\Http\Core\Controllers\User\Thread
 */
abstract class MessageControllerAbstract extends BaseControllerAbstract
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
     * @param \App\Athenia\Http\Core\Requests\User\Thread\Message\IndexRequest $request
     * @param User $user
     * @param Thread $thread
     * @return LengthAwarePaginator
     */
    public function index(\App\Athenia\Http\Core\Requests\User\Thread\Message\IndexRequest $request, User $user, Thread $thread)
    {
        $order = $this->order($request);

        if (!count($order)) {
            $order['created_at'] = 'desc';
        }

        return $this->repository->findAll($this->filter($request), $this->search($request), $order, $this->expand($request), $this->limit($request), [$thread], (int)$request->input('page', 1));
    }

    /**
     * @param \App\Athenia\Http\Core\Requests\User\Thread\Message\StoreRequest $request
     * @param User $user
     * @param Thread $thread
     * @return JsonResponse
     */
    public function store(\App\Athenia\Http\Core\Requests\User\Thread\Message\StoreRequest $request, User $user, Thread $thread) : JsonResponse
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
     * @param \App\Athenia\Http\Core\Requests\User\Thread\Message\UpdateRequest $request
     * @param User $user
     * @param Thread $thread
     * @param Message $message
     * @return BaseModelAbstract
     * @throws \Exception
     */
    public function update(\App\Athenia\Http\Core\Requests\User\Thread\Message\UpdateRequest $request, User $user, Thread $thread, Message $message)
    {
        $requestData = $request->json()->all();

        $data = [];

        if (isset($requestData['seen'])) {
            $data['seen_at'] = new Carbon();
        }

        return $this->repository->update($message, $data);
    }
}
