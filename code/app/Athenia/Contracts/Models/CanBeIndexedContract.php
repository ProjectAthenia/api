<?php
declare(strict_types=1);

namespace App\Athenia\Contracts\Models;

use App\Models\Resource;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Interface CanBeIndexedContract
 * @package App\Contracts\Models
 * @property Resource $resource
 * @property int $id
 */
interface CanBeIndexedContract extends CanBeMorphedToContract
{
    /**
     * Gets the content string to index
     *
     * @return string
     */
    public function getContentString(): ?string;

    /**
     * The resource object for this indexable model
     *
     * @return MorphOne
     */
    public function resource() : MorphOne;
}