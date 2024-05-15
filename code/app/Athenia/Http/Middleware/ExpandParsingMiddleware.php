<?php
declare(strict_types=1);

namespace App\Athenia\Http\Middleware;

use Closure;

/**
 * Class ExpandParsingMiddleware
 * @package App\Http\Middleware
 */
class ExpandParsingMiddleware
{
    /**
     * Add a parsed expand to the request as the `with` variable
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $with = [];
        
        if ($expand = $request->query('expand')) {
            if (is_array($expand)) {
                foreach ($expand as $field => $columns) {
                    if ($columns !== '*') throw new \DomainException('Column type of [' . $columns . '] is not yet implemented.');
                    $with[] = $field;
                }
            }
        }
        
        if ($with) {
            $request->query->set('with', $with);
        }
        
        return $next($request);
    }
}
