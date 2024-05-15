<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\User;

use App\Athenia\Contracts\Repositories\User\ProfileImageRepositoryContract;
use App\Athenia\Repositories\AssetRepository;
use App\Models\User\ProfileImage;
use App\Repositories\Traits\NotImplemented;
use Illuminate\Contracts\Filesystem\Factory;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class ProfileImageRepository
 * @package App\Repositories\User
 */
class ProfileImageRepository extends AssetRepository implements ProfileImageRepositoryContract
{
    use \App\Athenia\Repositories\Traits\NotImplemented\Update, \App\Athenia\Repositories\Traits\NotImplemented\Delete, \App\Athenia\Repositories\Traits\NotImplemented\FindAll;

    /**
     * ProfileImageRepository constructor.
     * @param ProfileImage $model
     * @param LogContract $log
     * @param Factory $fileSystem
     * @param string $assetBaseURL
     * @param string $basePublicDirectory
     */
    public function __construct(ProfileImage $model, LogContract $log, Factory $fileSystem,
                                string $assetBaseURL, string $basePublicDirectory)
    {
        parent::__construct($model, $log, $fileSystem, $assetBaseURL, $basePublicDirectory);
    }
}
