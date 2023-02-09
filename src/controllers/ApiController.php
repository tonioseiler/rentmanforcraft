<?php

namespace furbo\rentmanforcraft\controllers;

use Craft;
use craft\helpers\App;
use craft\web\Controller;
use furbo\rentmanforcraft\RentmanForCraft;
use yii\web\Response;

/**
 * Api controller
 */
class ApiController extends Controller
{
    public $defaultAction = 'index';

    protected array|int|bool $allowAnonymous = ['api', 'products', 'categories'];

    /**
     * rentman-for-craft/api action
     */
    public function actionIndex(): Response
    {
        //TODO: show the api version
    }

    /**
     * return all products
     * rentman-for-craft/api/products action
     * 
     * return all products of a category
     * /actions/rentman-for-craft/api/products?categoryId=167
     * 
     * return single product
     * /actions/rentman-for-craft/api/products?id=167
     * 
     */
    public function actionProducts(?int $categoryId = null, ?int $id = null): Response
    {
        if (!empty($id)) {
            $productsService = RentmanForCraft::getInstance()->productsService;
            return $this->asJson($productsService->getProductById($id));
        } else if (!empty($categoryId)) {
            $productsService = RentmanForCraft::getInstance()->productsService;
            return $this->asJson($productsService->getProductsByCategory($categoryId));
        } else {
            App::maxPowerCaptain();
            $productsService = RentmanForCraft::getInstance()->productsService;
            return $this->asJson($productsService->getAllProducts());
        }
    }

    /**
     * Return all main categories
     * rentman-for-craft/api/categories
     * 
     * Return all subcategories
     * /actions/rentman-for-craft/api/categories?parentId=183
     * 
     * Return a single category
     * /actions/rentman-for-craft/api/categories?id=183
     * 
     */
    public function actionCategories(?int $parentId = 0, ?int $id = null): Response
    {
        if (!empty($id)) {
            $categoriesService = RentmanForCraft::getInstance()->categoriesService;
            return $this->asJson($categoriesService->getCategoryById($id));
        } else {
            $categoriesService = RentmanForCraft::getInstance()->categoriesService;
            return $this->asJson($categoriesService->getCategories($parentId));
        }
    }

    /**
     * rentman-for-craft/api/projects action
     */
    public function actionProjects(): Response
    {
        //TODO: implement
    }

    /**
     * rentman-for-craft/api/add-product-to-project action
     */
    public function actionAddProductToProject(): Response
    {
        //TODO: implement
    }

    /**
     * rentman-for-craft/api/remove-product-from-project action
     */
    public function actionRemoveProductFromProject(): Response
    {
        //TODO: implement
    }

    /**
     * rentman-for-craft/api/submit-project action
     */
    public function actionSubmitProject(): Response
    {
        //TODO: implement
    }

    /**
     * rentman-for-craft/api/update-project action
     */
    public function actionUpdateProject(): Response
    {
        //TODO: implement
    }

    /**
     * rentman-for-craft/api/copy-project action
     */
    public function actionCopyProject(): Response
    {
        //TODO: implement
    }

    /**
     * rentman-for-craft/api/create-project action
     */
    public function actionCreateProject(): Response
    {
        //TODO: implement
    }

    /**
     * rentman-for-craft/api/delete-project action
     */
    public function actionDeleteProject(): Response
    {
        //TODO: implement
    }

    public function addProductToProject() {

    }

}
