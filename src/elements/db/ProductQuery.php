<?php

namespace furbo\rentmanforcraft\elements\db;

use Craft;
use craft\base\Element;
use craft\elements\db\ElementQuery;
use craft\helpers\Db;

/**
 * Product query
 */
class ProductQuery extends ElementQuery
{

    public $categoryId;

    public $rentmanId;

    public function categoryId($value)
    {
        $this->categoryId = $value;
        return $this;
    }

    public function rentmanId($value)
    {
        $this->rentmanId = $value;
        return $this;
    }


    protected function beforePrepare(): bool
    {
        $this->joinElementTable('rentman-for-craft_products');
        //$this->joinElementTable('rentman-for-craft_categories'); // paolo


        // select the collection id column
        $this->query->select([
            'rentman-for-craft_products.*',
            /*'rentman-for-craft_categories.displayname'*/ // paolo
        ]);

        if (isset($this->categoryId)) {
            $this->subQuery->andWhere(Db::parseParam('rentman-for-craft_products.categoryId', $this->categoryId));
        }

        if (isset($this->rentmanId)) {
            $this->subQuery->andWhere(Db::parseParam('rentman-for-craft_products.rentmanId', $this->rentmanId));
        }

        return parent::beforePrepare();

    }

    protected function statusCondition(string $status): mixed
    {
        return match ($status) {
            Element::STATUS_ENABLED => [
                'rentman-for-craft_products.in_shop' => true
            ],
            Element::STATUS_DISABLED => [
                'rentman-for-craft_products.in_shop' => false
            ],
            default => false,
        };
    }

    

}
