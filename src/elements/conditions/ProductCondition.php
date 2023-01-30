<?php

namespace furbo\rentmanforcraft\elements\conditions;

use Craft;
use craft\elements\conditions\ElementCondition;

/**
 * Product condition
 */
class ProductCondition extends ElementCondition
{
    protected function conditionRuleTypes(): array
    {
        return array_merge(parent::conditionRuleTypes(), [
            // ...
        ]);
    }
}
