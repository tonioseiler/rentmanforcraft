<?php

namespace furbo\rentmanforcraft\elements\db;

use Craft;
use craft\elements\db\ElementQuery;

/**
 * Category query
 */
class CategoryQuery extends ElementQuery
{

    public $parentId;

    public function parentId($value)
    {
        $this->parentId = $value;
        return $this;
    }

    protected function beforePrepare(): bool
    {
        // join in the items table
        $this->joinElementTable('rentman-for-craft_categories');

        if ($this->parentId) {
            $this->subQuery->andWhere(Db::parseParam('rentman-for-craft_categories.parent_id', $this->parentId));
        }

        return parent::beforePrepare();
    }
}
