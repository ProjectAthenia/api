<?php
declare(strict_types=1);

namespace App\Http\Core\Requests\Category;

use App\Http\Core\Requests\BaseUnauthenticatedRequest;
use App\Http\Core\Requests\Traits\HasNoExpands;
use App\Http\Core\Requests\Traits\HasNoRules;

/**
 * Class ViewRequest
 * @package App\Http\Core\Requests\Category
 */
class ViewRequest extends BaseUnauthenticatedRequest
{
    use HasNoRules, HasNoExpands;
}
