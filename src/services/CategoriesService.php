<?php

namespace furbo\rentmanforcraft\services;

use Craft;
use furbo\rentmanforcraft\elements\Category;
use yii\base\Component;

/**
 * Categories Service service
 */
class CategoriesService extends Component
{
    public function getCategories($parentId = 0)
    {
        $query = Category::find()
            ->parentId($parentId);
        return $query->all();
    }
}
