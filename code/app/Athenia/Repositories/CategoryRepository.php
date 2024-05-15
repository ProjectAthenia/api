<?php
declare(strict_types=1);

namespace App\Athenia\Repositories;

use App\Athenia\Contracts\Repositories\CategoryRepositoryContract;
use App\Models\Category;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class CategoryRepository
 * @package App\Repositories
 */
class CategoryRepository extends BaseRepositoryAbstract implements CategoryRepositoryContract
{
    /**
     * CategoryRepository constructor.
     * @param Category $model
     * @param LogContract $log
     */
    public function __construct(Category $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}