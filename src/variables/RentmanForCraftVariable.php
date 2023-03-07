<?php

namespace furbo\rentmanforcraft\variables;

use furbo\rentmanforcraft\RentmanForCraft;

use Craft;
use furbo\rentmanforcraft\elements\Category;
use furbo\rentmanforcraft\elements\Project;

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

    public function getCategories($parentId = 0)
    {
        $categoriesService = RentmanForCraft::getInstance()->categoriesService;
        return $categoriesService->getCategories($parentId);
    }
 public function getCategoryById($categoryId = 0)
    {
        $categoriesService = RentmanForCraft::getInstance()->categoriesService;
        return $categoriesService->getCategoryById($categoryId);
    }

    public function printCategoryTree($fullTree = false, $activeCategoryId = 0, $parentId = 0)
    {
        $ret = '';

        $activeCatIds = [];
        if (!empty($activeCategoryId)) {
            $tmp = Category::find()->id($activeCategoryId)->one();
            while (!$tmp->isMainCategory()) {
                $activeCatIds[] = $tmp->id;
                $tmp = $tmp->getParent();
            }
            $activeCatIds[] = $tmp->id;
        }

        if ($fullTree) {
            $categories = $this->getCategories($parentId);
            foreach($categories as $cat) {
                $ret .= '<li class="'.(in_array($cat->id, $activeCatIds) ? 'active' : '').'"><a href="'.$cat->getUrl().'">'.$cat->displayname.'</a>';
                if ($cat->hasChildren()) {
                    $ret .= '<ul>';
                    $ret .= $this->printCategoryTree(true, $activeCategoryId, $cat->id);
                    $ret .= '</ul>';
                }
                $ret .= '</li>';

            }
        } else {
            if (empty($activeCategoryId)) {
                //just print the main cats
                $categories = $this->getCategories($parentId);
                foreach($categories as $cat) {
                    $ret .= '<li><a href="'.$cat->getUrl().'">'.$cat->displayname.'</a></li>';
                }
            } else {
                $categories = $this->getCategories($parentId);
                foreach($categories as $cat) {
                    $isActive = in_array($cat->id, $activeCatIds);
                    $ret .= '<li class="'.($isActive  ? 'active' : '').'"><a href="'.$cat->getUrl().'">'.$cat->displayname.'</a>';
                    if ($cat->hasChildren() && $isActive) {
                        $ret .= '<ul>';
                        $ret .= $this->printCategoryTree(false, $activeCategoryId, $cat->id);
                        $ret .= '</ul>';
                    }
                    $ret .= '</li>';
                }
            }
        }
        
        if (!empty($ret) && $parentId == 0) $ret = '<ul>'.$ret.'</ul>';
        return $ret;
    }


    public function getSetContent($productId)
    {
       
    }

    public function getProductAccesories($productId)
    {
       
    }

    public function getUserProjects(): array
    {
        $user = Craft::$app->getUser()->getIdentity();
        $projectService = RentmanForCraft::getInstance()->projectsService;
        $projects = $projectService->getUserProjects($user);
        return $projects;
    }

    
    public function getActiveProject(): ?Project
    {
        $projectService = RentmanForCraft::getInstance()->projectsService;
        $project = $projectService->getActiveProject();
        return $project;
    }

    public function getProjectProductQuantity($productId): int {
        $projectService = RentmanForCraft::getInstance()->projectsService;
        $project = $projectService->getActiveProject();
        if (empty($project))
            return 0;
        return $projectService->getProjectProductQuantity($productId, $project->id);
    }

    public function searchProducts($query) {
        $productsService = RentmanForCraft::getInstance()->productsService;
        $products = $productsService->searchProducts($query);
        return $products;
    }


}
