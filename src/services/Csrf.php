<?php
/**
 * @copyright Copyright (c) Chris Dyer
 */

namespace dyerc\litespeed\services;

use Craft;
use craft\base\Component;
use craft\helpers\UrlHelper;
use craft\web\View;

class Csrf extends Component
{
    public function inject(): void
    {
        $view = Craft::$app->getView();

        if (!Craft::$app->getRequest()->getIsCpRequest()) {
            $js = $this->injectionScript();
            $view->registerJs($js, View::POS_END);
        }
    }

    public function injectionScript($csrfTokenName = null): string
    {
        if (!$csrfTokenName) {
            $csrfTokenName = Craft::$app->getConfig()->getGeneral()
                ->csrfTokenName;
        }

        $retrieveUrl = UrlHelper::actionUrl("litespeed/csrf/get-token");

        return <<<JS
window.LiteSpeed = {
  tokenName: "{$csrfTokenName}",
};

function injectCsrf() {
  var inputs = document.getElementsByName(window.LiteSpeed.tokenName);
  
  if (inputs.length > 0) {
    var xhr = new XMLHttpRequest();
    xhr.onload = function() {
      if (xhr.status >= 200 && xhr.status <= 299) {
        var tokenData = JSON.parse(this.responseText);
        LiteSpeed.tokenValue = tokenData.token;
        
        for (var i = 0; i < inputs.length; i++) {
          inputs[i].setAttribute("value", tokenData.token);
        }
      }
    };
    
    xhr.open("GET", "{$retrieveUrl}");
    xhr.send();
  }
}

setTimeout(function() {
  injectCsrf();
}, 50);
JS;
    }
}
