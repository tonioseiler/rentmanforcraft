<?php

namespace furbo\rentmanforcraft\console\controllers;

use Craft;
use craft\console\Controller;
use craft\queue\jobs\ResaveElements;
use furbo\rentmanforcraft\elements\Category;
use furbo\rentmanforcraft\elements\Product;
use furbo\rentmanforcraft\RentmanForCraft;
use furbo\rentmanforcraft\services\RentmanService;
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
     * rentman-for-craft/update-products command
     */
    public function actionUpdateProducts(): int
    {
        return $this->runSync(function (RentmanService $rentmanService) {
            $rentmanService->updateProducts();
        });
    }

    public function actionUpdateCategories(): int
    {
        return $this->runSync(function (RentmanService $rentmanService) {
            $rentmanService->updateCategories();
        });
    }

    public function actionUpdateAll(): int
    {
        return $this->runSync(function (RentmanService $rentmanService) {
            $rentmanService->updateCategories();
            $rentmanService->updateProducts();
        });
    }

    /**
     * Runs a sync while holding the PID file, so only one sync runs at a time and the CP utility can show it as running.
     */
    private function runSync(callable $sync): int
    {
        $rentmanService = RentmanForCraft::getInstance()->rentmanService;

        // also removes a stale PID file
        if ($runningPid = $rentmanService->runningSyncPid()) {
            $this->stderr("A Rentman sync is already running (PID {$runningPid}). Aborting." . PHP_EOL);
            return ExitCode::TEMPFAIL;
        }

        // 'x' fails if another sync created the file in the meantime
        $pidPath = $rentmanService->getSyncPidPath();
        $handle = @fopen($pidPath, 'x');
        if ($handle === false) {
            $this->stderr("A Rentman sync is already running. Aborting." . PHP_EOL);
            return ExitCode::TEMPFAIL;
        }
        $pid = getmypid();
        fwrite($handle, (string)$pid);
        fclose($handle);

        $releaseLock = function () use ($pidPath, $pid) {
            if (file_exists($pidPath) && (int)@file_get_contents($pidPath) === $pid) {
                @unlink($pidPath);
            }
        };
        // covers fatal errors and time / memory limits
        register_shutdown_function($releaseLock);

        try {
            $sync($rentmanService);
        } finally {
            $releaseLock();
        }

        return ExitCode::OK;
    }

}
