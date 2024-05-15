<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Controllers\Traits;

use App\Athenia\Http\Core\Requests\BaseRequestAbstract;
use Illuminate\Foundation\Validation\ValidatesRequests;

/**
 * Trait HasIndexRequests
 *
 * Adds some functionality to a controller for dealing with index requests
 *
 * @package App\Http\Core\Controllers\Traits
 */
trait HasIndexRequests
{
    use ValidatesRequests, HasViewRequests;

    /**
     * Get the search statement
     *
     * @param BaseRequestAbstract $request
     * @return array
     */
    protected function filter(BaseRequestAbstract $request): array
    {
        return $request->input('cleaned_filter', []);
    }

    /**
     * Get the search statement
     *
     * @param BaseRequestAbstract $request
     * @return array
     */
    protected function search(BaseRequestAbstract $request): array
    {
        return $request->input('cleaned_search', []);
    }

    /**
     * Get the order passed in by the user
     *
     * @param BaseRequestAbstract $request
     * @return array
     */
    protected function order(BaseRequestAbstract $request): array
    {
        return $request->input('order', []);
    }

    /**
     * Validate and get the limit for pagination per page
     *
     * @param BaseRequestAbstract $request
     * @return int
     */
    protected function limit(BaseRequestAbstract $request): int
    {
        $this->validate($request, [
            'limit' => 'nullable|integer|min:1|max:100'
        ]);

        return (int) $request->input('limit', 10);
    }
}