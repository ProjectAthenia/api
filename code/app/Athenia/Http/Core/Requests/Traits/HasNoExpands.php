<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Traits;

/**
 * Trait HasNoExpands
 * @package App\Http\Core\Requests\Traits
 */
trait HasNoExpands
{
    /**
     * No expands allowed when using this trait
     *
     * @return array
     */
    public function allowedExpands(): array
    {
        return [];
    }
}