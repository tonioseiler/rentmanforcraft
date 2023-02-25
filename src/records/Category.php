<?php

namespace furbo\rentmanforcraft\records;

use Craft;
use craft\db\ActiveRecord;
use furbo\rentmanforcraft\elements\Category as CategoryElement;

/**
 * Category record
 *
 * @property int $id ID
 * @property int|null $parentId Parent ID
 * @property int $rentmanId Rentman ID
 * @property string|null $displayname Displayname
 * @property int|null $order Order
 * @property string|null $itemtype Itemtype
 * @property string $dateCreated Date created
 * @property string $dateUpdated Date updated
 * @property string $uid Uid
 */
class Category extends ElementRecord
{
    public static function tableName()
    {
        return '{{%rentman-for-craft_categories}}';
    }

    public function getElement() {
        if (empty($this->element)) {
            $this->element = CategoryElement::findOne($this->id);
        }
        return $this->element;
    }
}
