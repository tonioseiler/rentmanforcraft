<?php

namespace furbo\rentmanforcraft\console\controllers;

use Craft;
use craft\console\Controller;
use furbo\rentmanforcraft\RentmanForCraft;
use yii\console\ExitCode;

/**
 * Import Products controller
 */
class RentmanController extends Controller
{
    public $defaultAction = 'update-products';

    public function options($actionID): array
    {
        $options = parent::options($actionID);
        switch ($actionID) {
            case 'update-products':
                // $options[] = '...';
                break;
        }
        return $options;
    }

    /**
     * rentman-for-craft/import-products command
     */
    public function actionUpdateProducts(): int
    {
        $rentmanService = RentmanForCraft::getInstance()->rentmanService;
        $rentmanService->updateProducts();
        return ExitCode::OK;
    }
}
