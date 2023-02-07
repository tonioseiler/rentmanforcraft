<?php

namespace furbo\rentmanforcraft\services;

use Craft;
use furbo\rentmanforcraft\elements\Product;
use yii\base\Component;

/**
 * Products Service service
 */
class ProductsService extends Component
{

    public function getAllProducts()
    {
        return Product::find()
            ->all();    
    }

    public function getProductById($id)
    {
        return Product::find()
            ->id($id)
            ->one();  
    }

    public function getProductsByCategory($categoryId)
    {
        return Product::find()
            ->categoryId($categoryId)
            ->all();  
    }

    public function getSetContent($productId)
    {
       
    }

    public function getProductAccesories($productId)
    {
       
    }


}
