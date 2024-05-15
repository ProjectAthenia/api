<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Traits;

/**
 * Trait HasNoPolicyParameters
 * @package App\Http\Core\Requests\Traits
 */
trait HasNoPolicyParameters
{
    /**
     * Gets any additional parameters needed for the policy function
     *
     * @return array
     */
    protected function getPolicyParameters(): array
    {
        return [];
    }
}