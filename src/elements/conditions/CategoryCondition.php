<?php

namespace furbo\rentmanforcraft\elements\conditions;

use Craft;
use craft\elements\conditions\ElementCondition;

/**
 * Category condition
 */
class CategoryCondition extends ElementCondition
{
    protected function conditionRuleTypes(): array
    {
        return array_merge(parent::conditionRuleTypes(), [
            // ...
        ]);
    }
}
