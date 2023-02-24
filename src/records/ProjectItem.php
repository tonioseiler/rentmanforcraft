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

    public function getProject() {
        return $this->hasOne(Project::className(), ['id' => 'projectId']);
    }

    public function getProduct() {
        return $this->hasOne(Product::className(), ['id' => 'productId']);
    }
}
