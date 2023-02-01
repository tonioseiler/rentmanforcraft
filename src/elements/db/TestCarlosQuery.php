<?php

namespace furbo\rentmanforcraft\elements\db;

use Craft;
use craft\elements\db\ElementQuery;

/**
 * Test Carlos query
 */
class TestCarlosQuery extends ElementQuery
{
    protected function beforePrepare(): bool
    {
        // todo: join the `testcarlos` table
        // $this->joinElementTable('testcarlos');

        // todo: apply any custom query params
        // ...

        return parent::beforePrepare();
    }
}
