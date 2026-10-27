<?php

namespace furbo\rentmanforcraft\controllers;

use Craft;
use craft\web\Controller;
use furbo\rentmanforcraft\RentmanForCraft;
use furbo\rentmanforcraft\services\RentmanService;
use yii\web\Response;

/**
 * Sync controller: starts the Rentman sync from the CP utility and reports whether it's running
 */
class SyncController extends Controller
{
    // Raw console output of the last sync started from the CP
    public const CONSOLE_LOG_FILE = '@storage/logs/rentman-sync-console.log';

    public function beforeAction($action): bool
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $this->requireCpRequest();
        $this->requirePermission('utility:rentman-sync');

        return true;
    }

    /**
     * rentman-for-craft/sync/start action
     * Starts rentman-for-craft/rentman/update-all in the background
     */
    public function actionStart(): Response
    {
        $this->requirePostRequest();

        $rentmanService = $this->rentmanService();

        if ($pid = $rentmanService->runningSyncPid()) {
            return $this->asJson(['started' => false, 'running' => true, 'error' => Craft::t('rentman-for-craft', 'A sync is already running.')]);
        }

        if (!function_exists('shell_exec')) {
            return $this->asJson(['started' => false, 'running' => false, 'error' => Craft::t('rentman-for-craft', 'The sync can’t be started from the control panel because shell_exec() is disabled on this server.')]);
        }

        $cmd = sprintf(
            '%s %s rentman-for-craft/rentman/update-all > %s 2>&1 & echo $!',
            escapeshellarg($this->phpBinary()),
            escapeshellarg(Craft::getAlias('@root') . '/craft'),
            escapeshellarg(Craft::getAlias(self::CONSOLE_LOG_FILE))
        );

        $pid = (int)shell_exec($cmd);

        if ($pid <= 0) {
            return $this->asJson(['started' => false, 'running' => false, 'error' => Craft::t('rentman-for-craft', 'The sync could not be started.')]);
        }

        Craft::info('Sync started from the CP by ' . Craft::$app->getUser()->getIdentity()->username . " (PID {$pid})", 'rentman-for-craft');

        return $this->asJson(['started' => true, 'running' => true, 'pid' => $pid]);
    }

    /**
     * rentman-for-craft/sync/status action
     * Optional `pid` (returned by start): counts as running until the command has written the PID file
     */
    public function actionStatus(): Response
    {
        $rentmanService = $this->rentmanService();
        $startedPid = (int)$this->request->getQueryParam('pid');

        $running = $rentmanService->runningSyncPid() !== null
            || ($startedPid > 0 && $rentmanService->isSyncProcessRunning($startedPid));

        return $this->asJson(['running' => $running]);
    }

    /**
     * The PHP CLI binary: in a web request PHP_BINARY is php-fpm or php-cgi, so look for the php next to it
     */
    private function phpBinary(): string
    {
        // not App::env(): it falls back to the PHP_BINARY constant
        if ($phpBinary = $_SERVER['PHP_BINARY'] ?? getenv('PHP_BINARY')) {
            return $phpBinary;
        }
        if (PHP_SAPI === 'cli') {
            return PHP_BINARY;
        }
        foreach ([dirname(PHP_BINARY) . '/php', PHP_BINDIR . '/php'] as $candidate) {
            if (is_file($candidate) && is_executable($candidate)) {
                return $candidate;
            }
        }
        // php-cgi can run console commands too
        return PHP_BINARY;
    }

    private function rentmanService(): RentmanService
    {
        return RentmanForCraft::getInstance()->rentmanService;
    }
}
