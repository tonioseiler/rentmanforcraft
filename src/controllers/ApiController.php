<?php

namespace furbo\rentmanforcraft\controllers;

use Craft;
use craft\web\Controller;
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
     * rentman-for-craft/api/products action
     */
    public function actionProducts(): Response
    {
        $productsService = RentmanForCraft::getInstance()->productsService;
        return $productsService->getAllProducts();
    }

    public function actionProduct($id): Response
    {
        $productsService = RentmanForCraft::getInstance()->productsService;
        return $productsService->getProductById($id);
    }

    /**
     * rentman-for-craft/api/categories action
     */
    public function actionCategories(): Response
    {
        //TODO: implement
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

}
