<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\Traits\NotImplemented;

use App\Athenia\Exceptions\NotImplementedException;
use App\Athenia\Models\BaseModelAbstract;

/**
 * Class Update
 * @package App\Repositories\Traits\NotImplemented
 */
trait Update
{
    /**
     * @param BaseModelAbstract $model
     * @param array $data
     * @param array $forcedValues
     * @return BaseModelAbstract
     */
    public function update(BaseModelAbstract $model, array $data, array $forcedValues = []): BaseModelAbstract
    {
        throw new NotImplementedException();
    }
}