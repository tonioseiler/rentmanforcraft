<?php

namespace furbo\rentmanforcraft\controllers;

use Craft;
use craft\web\Controller;
use yii\web\Response;

/**
 * User controller
 */
class UserController extends Controller
{
    public $defaultAction = 'index';
    protected array|int|bool $allowAnonymous = self::ALLOW_ANONYMOUS_NEVER;

    /**
     * rentman-for-craft/user action
     */
    public function actionIndex(): Response
    {
        // ...
    }
}
