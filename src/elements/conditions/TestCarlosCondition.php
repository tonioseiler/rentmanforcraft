<?php

namespace furbo\rentmanforcraft\elements\conditions;

use Craft;
use craft\elements\conditions\ElementCondition;

/**
 * Test Carlos condition
 */
class TestCarlosCondition extends ElementCondition
{
    protected function conditionRuleTypes(): array
    {
        return array_merge(parent::conditionRuleTypes(), [
            // ...
        ]);
    }
}
