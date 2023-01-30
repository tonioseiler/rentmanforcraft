<?php

namespace furbo\rentmanforcraft\elements\db;

use Craft;
use craft\elements\db\ElementQuery;

/**
 * Product query
 */
class ProductQuery extends ElementQuery
{

    public $categoryId;

    public function categoryId($value)
    {
        $this->categoryId = $value;
        return $this;
    }


    protected function beforePrepare(): bool
    {
        $this->joinElementTable('rentman-for-craft_products');

        if ($this->categoryId) {
            $this->subQuery->andWhere(Db::parseParam('rentman-for-craft_categories.category_id', $this->categoryId));
        }

        return parent::beforePrepare();

    }
}
