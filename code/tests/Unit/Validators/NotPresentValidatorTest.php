<?php
declare(strict_types=1);

namespace Tests\Unit\Validators;

use App\Validators\NotPresentValidator;
use Illuminate\Validation\Validator;
use Illuminate\Contracts\Translation\Translator;
use Tests\TestCase;

/**
 * Class NotPresentValidatorTest
 * @package Tests\Unit\Validators
 */
class NotPresentValidatorTest extends TestCase 
{
    public function testNotPresentTrue()
    {
        $validator = new NotPresentValidator();
        
        $translatorMock = mock(Translator::class);
        $validatorObject = new Validator($translatorMock, ['item_here'=>1], []);
        
        $this->assertTrue($validator->validate('not_here', null, [], $validatorObject));
    }
    
    public function testNotPresentFalse()
    {
        $validator = new NotPresentValidator();

        $translatorMock = mock(Translator::class);
        $validatorObject = new Validator($translatorMock, ['item_here_value'=>1, 'item_here_null' => null], []);

        $this->assertFalse($validator->validate('item_here_value', 1, [], $validatorObject));
        $this->assertFalse($validator->validate('item_here_null', null, [], $validatorObject));
    }
}