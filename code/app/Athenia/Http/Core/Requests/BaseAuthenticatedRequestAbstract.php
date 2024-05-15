<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Class BaseRequestAbstract
 * @package App\Http\Core\Requests
 */
abstract class BaseAuthenticatedRequestAbstract extends BaseRequestAbstract
{
    use AuthorizesRequests {
        AuthorizesRequests::authorize as authorizeRequest;
    }

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    abstract protected function getPolicyAction(): string;

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    abstract protected function getPolicyModel() : string;

    /**
     * Gets any additional parameters needed for the policy function
     *
     * @return array
     */
    abstract protected function getPolicyParameters() : array;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorize() : bool
    {
        $this->authorizeExpands();
        $parameters = array_merge([$this->getPolicyModel()], $this->getPolicyParameters());
        $this->authorizeRequest($this->getPolicyAction(), $parameters);
        return true;
    }
}