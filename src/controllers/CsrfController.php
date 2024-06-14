<?php
/**
 * @copyright Copyright (c) Chris Dyer
 */

namespace dyerc\litespeed\controllers;

use Craft;
use craft\web\Controller;
use yii\web\Response;

class CsrfController extends Controller
{
    protected array|bool|int $allowAnonymous = true;

    public function actionGetToken(): Response
    {
        $request = Craft::$app->getRequest();

        return $this->asJson([
            "token" => $request->getCsrfToken(),
            "name" => $request->csrfParam,
        ]);
    }
}
