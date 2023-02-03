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

    public $rentmanId;

    public function parentId($value)
    {
        $this->parentId = $value;
        return $this;
    }

    public function rentmanId($value)
    {
        $this->rentmanId = $value;
        return $this;
    }

    protected function beforePrepare(): bool
    {
        // join in the items table
        $this->joinElementTable('rentman-for-craft_categories');

        $this->query->select([
            'rentman-for-craft_categories.*'
        ]);

        if ($this->parentId) {
            $this->subQuery->andWhere(Db::parseParam('rentman-for-craft_categories.parentId', $this->parentId));
        }

        if ($this->rentmanId) {
            $this->subQuery->andWhere(Db::parseParam('rentman-for-craft_categories.rentmanId', $this->rentmanId));
        }

        return parent::beforePrepare();
    }
}
