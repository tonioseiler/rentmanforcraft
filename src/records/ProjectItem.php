<?php

namespace furbo\rentmanforcraft\records;

use Craft;
use craft\db\ActiveRecord;

/**
 * Project Item record
 */
class ProjectItem extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%rentman-for-craft_projectitems}}';
    }
}
