<?php
declare(strict_types=1);

namespace App\Athenia\Validators;

use RuntimeException;

/**
 * Class BaseValidatorAbstract
 * @package App\Validators
 */
abstract class BaseValidatorAbstract
{
    /**
     * Makes sure that the
     *
     * @param $expected
     * @param $actual
     * @throws RuntimeException
     */
    public function ensureValidatorAttribute($expected, $actual)
    {
        if ($expected != $actual) {
            throw new RuntimeException('Validator registered to the wrong validation attribute. Please make sure that you only use this validator on the ' . $expected . ' attribute.');
        }
    }
}