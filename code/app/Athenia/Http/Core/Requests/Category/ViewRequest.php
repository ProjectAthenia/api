<?php
declare(strict_types=1);

namespace App\Athenia\Http\Core\Requests\Category;

use App\Athenia\Http\Core\Requests\BaseUnauthenticatedRequest;
use App\Athenia\Http\Core\Requests\Traits\HasNoExpands;
use App\Athenia\Http\Core\Requests\Traits\HasNoRules;

/**
 * Class ViewRequest
 * @package App\Http\Core\Requests\Category
 */
class ViewRequest extends BaseUnauthenticatedRequest
{
    use HasNoRules, HasNoExpands;
}
