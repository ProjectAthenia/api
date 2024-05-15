<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests;

/**
 * Class BaseUnauthenticatedRequest
 * @package App\Http\Core\Requests
 */
abstract class BaseUnauthenticatedRequest extends BaseRequestAbstract
{
    /**
     * Whether or not the current user is authenticated to run this request
     *
     * @return bool
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorize(): bool
    {
        $this->authorizeExpands();
        return true;
    }
}