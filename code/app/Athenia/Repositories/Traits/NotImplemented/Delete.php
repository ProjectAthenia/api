<?php
declare(strict_types=1);

namespace App\Athenia\Repositories\Traits\NotImplemented;

use App\Athenia\Exceptions\NotImplementedException;
use App\Athenia\Models\BaseModelAbstract;

/**
 * Class Delete
 * @package App\Repositories\Traits\NotImplemented
 */
trait Delete
{
    /**
     * Not implemented
     * 
     * @param BaseModelAbstract $model
     * @return bool|null|void
     * @throws NotImplementedException
     */
    public function delete(BaseModelAbstract $model)
    {
        throw new NotImplementedException();
    }
}