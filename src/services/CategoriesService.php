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

    public function getCategoriesRecursive($parentId = 0)
    {
        //$allCategories = array();
        $query = Category::find()
            ->parentId($parentId);

        $categories = $query->orderBy('order')->all();
        foreach ($categories as $category) {
            //$allCategories[] = $category->id;

            $allCategories[] = [
                'id' => $category->id,
                'uri' => $category->uri,
                'displayname' => $category->displayname
            ];



            $tempSubCategories = $this->getCategoriesRecursive($category->id);
            if ($tempSubCategories != null) {
                $allCategories[] = $tempSubCategories;
            }
        }


        if (isset($allCategories)) {
            return $allCategories;
        } else {
            return null;
        }
    }



    public function getCategoryById($id)
    {
        return Category::find()
            ->id($id)
            ->one();  
    }
}
