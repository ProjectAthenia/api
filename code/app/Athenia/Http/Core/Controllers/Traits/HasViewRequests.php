<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Controllers\Traits;

use App\Athenia\Http\Core\Requests\BaseRequestAbstract;

/**
 * Trait HasViewRequests
 *
 * Adds some functionality to the controller for expand requests
 *
 * @package App\Http\Core\Controllers\Traits
 */
trait HasViewRequests
{
    /**
     * Get the expanded / width statement
     *
     * @param BaseRequestAbstract $request
     * @return array
     */
    protected function expand(BaseRequestAbstract $request): array
    {
        return $request->input('with', []);
    }
}