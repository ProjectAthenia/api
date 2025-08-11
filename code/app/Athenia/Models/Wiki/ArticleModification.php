<?php
declare(strict_types=1);

namespace App\Models\Wiki;

use App\Athenia\Models\BaseModelAbstract;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class ArticleModification
 * @package App\Models\Wiki
 */
class ArticleModification extends BaseModelAbstract
{
    /**
     * The article this modification is from
     *
     * @return BelongsTo
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * @return HasOne
     */
    public function iteration(): HasOne
    {
        return $this->hasOne(ArticleIteration::class);
    }
}
