<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\User;

use App\Athenia\Contracts\Repositories\User\PasswordTokenRepositoryContract;
use App\Athenia\Contracts\Services\TokenGenerationServiceContract;
use App\Athenia\Events\User\ForgotPasswordEvent;
use App\Athenia\Models\BaseModelAbstract;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use App\Models\User\PasswordToken;
use App\Models\User\User;
use App\Repositories\Traits\NotImplemented;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Model;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class PasswordTokenRepository
 * @package App\Repositories\User
 */
class PasswordTokenRepository extends BaseRepositoryAbstract implements PasswordTokenRepositoryContract
{
    use \App\Athenia\Repositories\Traits\NotImplemented\Update, \App\Athenia\Repositories\Traits\NotImplemented\FindAll, \App\Athenia\Repositories\Traits\NotImplemented\FindOrFail, \App\Athenia\Repositories\Traits\NotImplemented\Delete;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * @var TokenGenerationServiceContract
     */
    private $tokenGenerationService;

    /**
     * PasswordTokenRepository constructor.
     * @param PasswordToken $model
     * @param LogContract $log
     * @param Dispatcher $dispatcher
     * @param TokenGenerationServiceContract $tokenGenerationService
     */
    public function __construct(PasswordToken $model, LogContract $log, Dispatcher $dispatcher,
                                TokenGenerationServiceContract $tokenGenerationService)
    {
        parent::__construct($model, $log);
        $this->dispatcher = $dispatcher;
        $this->tokenGenerationService = $tokenGenerationService;
    }

    /**
     * Overrides the parent in order to dispatch the forgot password event
     *
     * @param array $data
     * @param BaseModelAbstract|null $relatedModel
     * @param array $forcedValues
     * @return PasswordToken
     */
    public function create(array $data = [], BaseModelAbstract $relatedModel = null, array $forcedValues = [])
    {
        /** @var PasswordToken $passwordToken */
        $passwordToken = parent::create($data, $relatedModel, $forcedValues);

        $this->dispatcher->dispatch(new ForgotPasswordEvent($passwordToken));

        return $passwordToken;
    }

    /**
     * Searches for a password token model owned by a user with a token
     *
     * @param User $user
     * @param string $token
     * @return Model|PasswordToken|null
     */
    public function findForUser(User $user, string $token): ?PasswordToken
    {
        return $this->model->newQuery()
            ->where('user_id', '=', $user->id)
            ->where('token', '=', $token)
            ->first();
    }

    /**
     * Generates a unique token for a user, or throws an exception if it cannot do so.
     *
     * @param User $user
     * @throws \OverflowException
     * @return string
     */
    public function generateUniqueToken(User $user): string
    {
        $attempts = 0;
        do {
            $token = $this->tokenGenerationService->generateToken();
            $existingModel = $this->findForUser($user, $token);
            $attempts++;
        } while ($existingModel != null && $attempts < 5);

        if ($existingModel) {
            throw new \OverflowException('Unable to generate unique token for the user.');
        }

        return $token;
    }
}