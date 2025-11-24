<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\User;

use App\Athenia\Contracts\Repositories\User\InvitationTokenRepositoryContract;
use App\Athenia\Contracts\Services\TokenGenerationServiceContract;
use App\Athenia\Models\User\InvitationToken;
use App\Athenia\Repositories\BaseRepositoryAbstract;
use Illuminate\Database\Eloquent\Model;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class InvitationTokenRepository
 * @package App\Repositories\User
 */
class InvitationTokenRepository extends BaseRepositoryAbstract implements InvitationTokenRepositoryContract
{
    use \App\Athenia\Repositories\Traits\NotImplemented\FindAll, \App\Athenia\Repositories\Traits\NotImplemented\FindOrFail, \App\Athenia\Repositories\Traits\NotImplemented\Delete;

    /**
     * @var TokenGenerationServiceContract
     */
    private TokenGenerationServiceContract $tokenGenerationService;

    /**
     * InvitationTokenRepository constructor.
     * @param InvitationToken $model
     * @param LogContract $log
     * @param TokenGenerationServiceContract $tokenGenerationService
     */
    public function __construct(InvitationToken $model, LogContract $log,
                                TokenGenerationServiceContract $tokenGenerationService)
    {
        parent::__construct($model, $log);
        $this->tokenGenerationService = $tokenGenerationService;
    }

    /**
     * Finds an invitation token by its token string
     *
     * @param string $token
     * @return Model|InvitationToken|null
     */
    public function findByToken(string $token): ?InvitationToken
    {
        return $this->model->newQuery()
            ->where('token', '=', $token)
            ->first();
    }

    /**
     * Generates a unique token, or throws an exception if it cannot do so.
     *
     * @throws \OverflowException
     * @return string
     */
    public function generateUniqueToken(): string
    {
        $attempts = 0;
        do {
            $token = $this->tokenGenerationService->generateToken();
            $existingModel = $this->findByToken($token);
            $attempts++;
        } while ($existingModel != null && $attempts < 5);

        if ($existingModel) {
            throw new \OverflowException('Unable to generate unique invitation token.');
        }

        return $token;
    }
}
