<?php
declare(strict_types=1);

namespace App\Contracts\Models;

/**
 * Interface HasValidationRulesContract
 * @package App\Contracts\Models
 */
interface HasValidationRulesContract
{
    /**
     * @var string the base validation rules
     */
    const VALIDATION_RULES_BASE = 'base';

    /**
     * @var string the create validation rules
     */
    const VALIDATION_RULES_CREATE = 'create';

    /**
     * @var string the update validation rules
     */
    const VALIDATION_RULES_UPDATE = 'update';

    /**
     * @var string the modifier of making something required
     */
    const VALIDATION_PREPEND_REQUIRED = 'prepend-required';

    /**
     * @var string this field should not exist
     */
    const VALIDATION_PREPEND_NOT_PRESENT = 'prepend-not_present';

    /**
     * @var string this field should be allowed to be set as null
     */
    const VALIDATION_PREPEND_NULLABLE = 'prepend-nullable';

    /**
     * Get Validation Rules
     *
     * @param string|null $context
     * @return array
     */
    public function getValidationRules(string $context = null): array;

    /**
     * Build the model validation rules
     * @param array $params
     * @return array
     */
    public function buildModelValidationRules(...$params): array;
}