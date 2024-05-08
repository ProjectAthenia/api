<?php
declare(strict_types=1);

namespace App\Contracts\Models;

/**
 * Interface CanBeMorphedTo
 * @package App\Contracts\Models
 */
interface CanBeMorphedToContract
{
    /**
     * The name of the morph relation
     *
     * @return string
     */
    public function morphRelationName(): string;
}