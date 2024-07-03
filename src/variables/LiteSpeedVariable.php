<?php

namespace dyerc\litespeed\variables;

use Craft;
use craft\helpers\Html;
use craft\helpers\Template;
use dyerc\litespeed\LiteSpeed;

class LiteSpeedVariable
{
    public function injectCsrf()
    {
        $request = Craft::$app->getRequest();

        $script = LiteSpeed::getInstance()->csrf->injectionScript();

        $output = Html::tag("script", $script);
        $input = Html::hiddenInput($request->csrfParam, $request->csrfToken);

        return Template::raw($input . "\n" . $output);
    }
}
