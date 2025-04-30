<?php
declare(strict_types=1);

namespace App\Http\Core\Requests\Statistics;

use App\Athenia\Http\Core\Requests\Statistics\ViewRequestAbstract;

/**
 * Class ViewRequest
 * @package App\Http\Core\Requests\Statistics
 */
class ViewRequest extends ViewRequestAbstract
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [];
    }
} 