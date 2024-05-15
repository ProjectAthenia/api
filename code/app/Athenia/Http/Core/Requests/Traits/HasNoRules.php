<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Traits;

/**
 * Trait HasNoRules
 * @package App\Http\Core\Requests\Traits
 */
trait HasNoRules
{
    /**
     * Default Rules
     *
     * @return array
     */
    public function rules(): array
    {
        return [];
    }
}