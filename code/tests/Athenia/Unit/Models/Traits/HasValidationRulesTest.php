<?php
declare(strict_types=1);

namespace Test\Athenia\Unit\Models\Traits;

use App\Athenia\Contracts\Models\HasValidationRulesContract;
use App\Athenia\Models\Traits\HasValidationRules;
use Tests\TestCase;

/**
 * Class HasValidationRulesTest
 * @package Tests\Unit\Traits\Models
 */
class HasValidationRulesTest extends TestCase
{
    public function testBaseNotSet()
    {
        $model = new class implements HasValidationRulesContract {
            use HasValidationRules;
            public function buildModelValidationRules(...$params): array
            {
                return [];
            }
        };
        $this->assertEmpty($model->getValidationRules());
    }

    public function testGetSimpleRules()
    {
        $model = new class implements HasValidationRulesContract {
            use HasValidationRules;
            public function buildModelValidationRules(...$params): array
            {
                return [
                    HasValidationRulesContract::VALIDATION_RULES_BASE => ['hi']
                ];
            }
        };
        $this->assertEquals(['hi'], $model->getValidationRules());
    }

    public function testContextSetButDoesNotExist()
    {
        $model = new class implements HasValidationRulesContract {
            use HasValidationRules;
            public function buildModelValidationRules(...$params): array
            {
                return [
                    HasValidationRulesContract::VALIDATION_RULES_BASE => ['hi']
                ];
            }
        };
        $this->assertEquals(['hi'], $model->getValidationRules('notExist'));
    }

    public function testContextSetButDoesNotMatch()
    {
        $model = new class implements HasValidationRulesContract {
            use HasValidationRules;
            public function buildModelValidationRules(...$params): array
            {
                return [
                    HasValidationRulesContract::VALIDATION_RULES_BASE => ['hi' => 'there'],
                    'context-here' => ['prepend-non' => ['here']]
                ];
            }
        };
        $this->assertEquals(['hi' => 'there'], $model->getValidationRules('context-here'));
    }

    public function testContextPrepends()
    {
        $model = new class implements HasValidationRulesContract {
            use HasValidationRules;
            public function buildModelValidationRules(...$params): array
            {
                return [
                    HasValidationRulesContract::VALIDATION_RULES_BASE => ['property_name' => ['integer']],
                    'update-context' => ['prepend-required' => ['property_name']]
                ];
            }
        };
        $this->assertEquals(['property_name' => ['required', 'integer']], $model->getValidationRules('update-context'));
    }
}
