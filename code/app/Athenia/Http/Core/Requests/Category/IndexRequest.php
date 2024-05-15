<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Category;

use App\Athenia\Http\Core\Requests\BaseUnauthenticatedRequest;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use App\Athenia\Http\Core\Requests\Traits\HasNoRules;

/**
 * Class IndexRequest
 * @package App\Http\Core\Requests\Category
 */
class IndexRequest extends BaseUnauthenticatedRequest
{
    use HasNoRules, HasNoExpands;
}