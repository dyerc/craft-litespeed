<?php
/**
 * @copyright Copyright (c) Chris Dyer
 */

namespace dyerc\litespeed\console\controllers;

use craft\console\Controller;
use dyerc\litespeed\LiteSpeed;
use yii\console\ExitCode;

class CacheController extends Controller
{
    /**
     * @inheritdoc
     */
    public $defaultAction = "clear";

    public function actionClear()
    {
        $cleared = LiteSpeed::getInstance()->cache->clearAll();

        if ($cleared === true) {
            $this->stdout("Cleared LiteSpeed cache");

            return ExitCode::OK;
        } else {
            $this->stdout("Failed to clear LiteSpeed cache: " . $cleared);

            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
}
