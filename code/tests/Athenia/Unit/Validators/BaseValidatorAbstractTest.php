<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Validators;

use App\Athenia\Validators\BaseValidatorAbstract;
use RuntimeException;
use Tests\Mocks\BaseValidator;
use Tests\TestCase;

/**
 * Class BaseValidatorAbstractTest
 * @package Tests\Athenia\Unit\Validators
 */
final class BaseValidatorAbstractTest extends TestCase
{
    public function testEnsureValidatorAttributePasses(): void
    {
        $validator = new BaseValidator();

        $validator->ensureValidatorAttribute('hello', 'hello');
    }

    public function testEnsureValidatorAttributeThrowsException(): void
    {
        $this->expectException(RuntimeException::class);

        $validator = new BaseValidator();

        $validator->ensureValidatorAttribute('hello', 'hi');
    }
}