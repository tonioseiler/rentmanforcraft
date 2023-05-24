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
        return $query->orderBy('order')->all();
    }

    public function getCategoriesRecursive($parentId = 0, $parentDisplayName = '')
    {
        $query = Category::find()
            ->parentId($parentId);
        $categories = $query->orderBy('order')->all();
        $allCategories = [];
        foreach ($categories as $category) {
            $currentDisplayName = $parentDisplayName !== '' ? $parentDisplayName . ' - ' . $category->displayname : $category->displayname;
            $tempSubCategories = $this->getCategoriesRecursive($category->id, $currentDisplayName);
            if (!empty($tempSubCategories)) {
                $allCategories[] = [
                    'id' => $category->id,
                    'uri' => $category->uri,
                    'displayname' => $currentDisplayName,
                    'haschildren' => 1,
                ];
                $allCategories = array_merge($allCategories, $tempSubCategories);
            } else {
                $allCategories[] = [
                    'id' => $category->id,
                    'uri' => $category->uri,
                    'displayname' => $currentDisplayName,
                    'haschildren' => 0,
                ];
            }
        }
        return $allCategories;
    }

    public function getCategoryById($id)
    {
        return Category::find()
            ->id($id)
            ->one();
    }
}
