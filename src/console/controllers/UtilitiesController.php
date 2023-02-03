<?php

namespace furbo\rentmanforcraft\console\controllers;

use Craft;
use craft\console\Controller;
use craft\queue\jobs\ResaveElements;
use furbo\rentmanforcraft\elements\Category;
use furbo\rentmanforcraft\elements\Product;
use yii\console\ExitCode;

/**
 * Resave Products controller
 */
class UtilitiesController extends Controller
{
    public $defaultAction = 'index';

    public function options($actionID): array
    {
        $options = parent::options($actionID);
        switch ($actionID) {
            case 'index':
                // $options[] = '...';
                break;
        }
        return $options;
    }

    public function actionResaveProducts(): int
    {
        Craft::$app->getQueue()->push(new ResaveElements([
            'elementType' => Product::class,
            'criteria' => [
                'siteId' => '*',
                'unique' => true,
                'status' => null,
            ],
        ]));
        return ExitCode::OK;
    }

    public function actionResaveCategories(): int
    {
        Craft::$app->getQueue()->push(new ResaveElements([
            'elementType' => Category::class,
            'criteria' => [
                'siteId' => '*',
                'unique' => true,
                'status' => null,
            ],
        ]));
        return ExitCode::OK;
    }
}
