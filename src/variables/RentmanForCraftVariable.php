<?php

namespace furbo\rentmanforcraft\variables;

use furbo\rentmanforcraft\RentmanForCraft;

use Craft;

/**
 * Rentman for Craft Variable
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.rentman }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    Furbo GmbH
 * @package   RentmanForCraft
 * @since     1.0.0
 */
class RentmanForCraftVariable
{
    // Public Methods
    // =========================================================================

    /**
     *
     *     {{ craft.rentman.cpTitle }} or
     *     {{ craft.rentamn.cpTitle(twigValue) }}
     *
     * @param null $optional
     * @return string
     */
    public function cpTitle($optional = null)
    {
        $settings = RentmanForCraft::getInstance()->getSettings();
        return $settings['cpTitle'];
    }

    public function getAllProducts()
    {
        $productsService = RentmanForCraft::getInstance()->productsService;
        return $productsService->getAllProducts();
    }

    public function getProductById($id)
    {
        $productsService = RentmanForCraft::getInstance()->productsService;
        return $productsService->getProductById($id);
    }

    public function getProductsByCategory($categoryId)
    {
        $productsService = RentmanForCraft::getInstance()->productsService;
        return $productsService->getProductsByCategory($categoryId);
    }

    

    public function getCategoryTree()
    {
       
    }

    public function printCategoryTree($activeCategoryId)
    {
       
    }


    public function getSetContent($productId)
    {
       
    }

    public function getProductAccesories($productId)
    {
       
    }



}
