<?php

namespace furbo\rentmanforcraft\records;

use Craft;
use craft\db\ActiveRecord;

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
class Category extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%rentman-for-craft_categories}}';
    }
}
