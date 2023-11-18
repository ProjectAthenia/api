<?php
declare(strict_types=1);

namespace App\Validators;

use Illuminate\Http\Request;
use Illuminate\Validation\Validator;

/**
 * Class OwnedByValidator
 * @package App\Validators
 */
class OwnedByValidator
{
    /**
     * The key for easy reference around the app
     */
    const KEY = 'owned_by';

    /**
     * @var Request
     */
    protected $request;

    /**
     * OwnedByValidator constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * This is invoked by the validator rule 'owned_by'
     *
     * @param $attribute
     * @param $value
     * @param array $parameters
     * @param Validator|null $validator
     * @return bool
     */
    public function validate($attribute, $value, $parameters = [], Validator $validator = null)
    {
        $ownerRequestParamName = array_shift($parameters);

        $ownerParam = $this->request->route($ownerRequestParamName);

        while (count($parameters) ) {
            $relation = array_shift($parameters);
            $relatedObject = $ownerParam->{$relation};
            $ownerParam = $relatedObject;
        }

        return $relatedObject->contains('id', $value);
    }
}
