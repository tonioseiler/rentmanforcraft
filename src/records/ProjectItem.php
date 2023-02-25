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
        return $this->hasOne(Project::class, ['id' => 'projectId'])->one();
    }

    public function getProduct() {
        return $this->hasOne(Product::class, ['id' => 'productId'])->one();
    }
}
