<?php

namespace furbo\rentmanforcraft\controllers;

use Craft;
use craft\web\Controller;
use yii\web\Response;

/**
 * Products controller
 */
class ProductsController extends Controller
{
    public $defaultAction = 'index';
    protected array|int|bool $allowAnonymous = self::ALLOW_ANONYMOUS_NEVER;

    /**
     * rentman-for-craft/products action
     */
    public function actionIndex(): Response
    {
        // ...
    }
}
