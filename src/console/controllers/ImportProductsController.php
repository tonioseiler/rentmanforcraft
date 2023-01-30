<?php

namespace furbo\rentmanforcraft\console\controllers;

use Craft;
use craft\console\Controller;
use yii\console\ExitCode;

/**
 * Import Products controller
 */
class ImportProductsController extends Controller
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

    /**
     * rentman-for-craft/import-products command
     */
    public function actionIndex(): int
    {
        // ...
        return ExitCode::OK;
    }
}
