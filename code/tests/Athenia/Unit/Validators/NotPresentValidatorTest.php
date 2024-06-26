<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Validators;

use App\Athenia\Validators\NotPresentValidator;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Validation\Validator;
use Tests\TestCase;

/**
 * Class NotPresentValidatorTest
 * @package Tests\Athenia\Unit\Validators
 */
final class NotPresentValidatorTest extends TestCase 
{
    public function testNotPresentTrue(): void
    {
        $validator = new NotPresentValidator();
        
        $translatorMock = mock(Translator::class);
        $validatorObject = new Validator($translatorMock, ['item_here'=>1], []);
        
        $this->assertTrue($validator->validate('not_here', null, [], $validatorObject));
    }
    
    public function testNotPresentFalse(): void
    {
        $validator = new NotPresentValidator();

        $translatorMock = mock(Translator::class);
        $validatorObject = new Validator($translatorMock, ['item_here_value'=>1, 'item_here_null' => null], []);

        $this->assertFalse($validator->validate('item_here_value', 1, [], $validatorObject));
        $this->assertFalse($validator->validate('item_here_null', null, [], $validatorObject));
    }
}