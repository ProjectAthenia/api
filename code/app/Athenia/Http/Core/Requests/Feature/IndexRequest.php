<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Feature;

use App\Athenia\Http\Core\Requests\BaseUnauthenticatedRequest;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;

/**
 * Class IndexRequest
 * @package App\Http\Core\Requests\Article
 */
class IndexRequest extends BaseUnauthenticatedRequest
{
    use HasNoExpands;
}
