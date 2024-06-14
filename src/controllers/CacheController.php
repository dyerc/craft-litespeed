<?php
/**
 * @copyright Copyright (c) Chris Dyer
 */

namespace dyerc\litespeed\controllers;

use Craft;
use craft\web\Controller;
use yii\web\Response;

class CacheController extends Controller
{
    protected array|bool|int $allowAnonymous = true;

    public function actionClear(): Response
    {
        $response = Craft::$app->getResponse();
        $headers = $response->headers;

        $headers->set("X-Litespeed-Cache-Control", "no-cache");
        $headers->set("X-LiteSpeed-Purge", "public, private, *");

        return $this->asJson([
            "status" => "ok",
        ]);
    }
}
