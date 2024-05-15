<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\Traits\NotImplemented;

use App\Athenia\Exceptions\NotImplementedException;
use App\Athenia\Models\BaseModelAbstract;

/**
 * Class Create
 * @package App\Repositories\Traits\NotImplemented
 */
trait Create
{
    /**
     * @param array $data
     * @param BaseModelAbstract|null $parentModel
     * @param array $forcedData
     * @return BaseModelAbstract|void
     */
    public function create(array $data = [], BaseModelAbstract $parentModel = null, array $forcedData = [])
    {
        throw new NotImplementedException();
    }
}