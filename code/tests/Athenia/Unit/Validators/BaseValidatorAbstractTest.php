<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Validators;

use App\Athenia\Validators\BaseValidatorAbstract;
use RuntimeException;
use Tests\TestCase;

/**
 * Class BaseValidatorAbstractTest
 * @package Tests\Athenia\Unit\Validators
 */
final class BaseValidatorAbstractTest extends TestCase
{
    public function testEnsureValidatorAttributePasses(): void
    {
        /** @var BaseValidatorAbstract $validator */
        $validator = $this->getMockForAbstractClass(BaseValidatorAbstract::class);

        $validator->ensureValidatorAttribute('hello', 'hello');
    }

    public function testEnsureValidatorAttributeThrowsException(): void
    {
        $this->expectException(RuntimeException::class);

        /** @var BaseValidatorAbstract $validator */
        $validator = $this->getMockForAbstractClass(BaseValidatorAbstract::class);

        $validator->ensureValidatorAttribute('hello', 'hi');
    }
}