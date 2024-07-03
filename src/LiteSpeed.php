<?php

namespace dyerc\litespeed;

use craft\base\Element;
use craft\base\Model;
use craft\base\Plugin;
use craft\events\ElementEvent;
use craft\events\RegisterCacheOptionsEvent;
use craft\helpers\ElementHelper;
use craft\services\Elements;
use craft\utilities\ClearCaches;
use craft\web\twig\variables\CraftVariable;
use dyerc\litespeed\models\Settings;
use dyerc\litespeed\services\Cache;
use dyerc\litespeed\services\Csrf;
use dyerc\litespeed\variables\LiteSpeedVariable;
use yii\base\Event;

/**
 * @property-read Cache $cache
 * @property-read Csrf $csrf
 */
class LiteSpeed extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var null|LiteSpeed
     */
    public static ?LiteSpeed $plugin;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        /* @var Settings $settings */
        $settings = $this->getSettings();

        $this->_registerServices();
        $this->_registerEvents();
        $this->_registerVariables();

        if ($settings->injectCsrf) {
            $this->csrf->inject();
        }
    }

    protected function createSettingsModel(): ?Model
    {
        return new Settings();
    }

    private function _registerServices(): void
    {
        $this->setComponents([
            "cache" => Cache::class,
            "csrf" => Csrf::class,
        ]);
    }

    private function _registerEvents(): void
    {
        Event::on(
            Elements::class,
            Elements::EVENT_AFTER_SAVE_ELEMENT,
            function (ElementEvent $event) {
                $this->_handleCraftEvent($event);
            }
        );

        Event::on(
            ClearCaches::class,
            ClearCaches::EVENT_REGISTER_CACHE_OPTIONS,
            function (RegisterCacheOptionsEvent $event) {
                $event->options[] = [
                    "key" => "litespeed",
                    "label" => "LiteSpeed cache",
                    "action" => function () {
                        LiteSpeed::$plugin->cache->clearAll();
                    },
                ];
            }
        );
    }

    private function _registerVariables(): void
    {
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function (
            Event $event
        ) {
            /** @var CraftVariable $variable */
            $variable = $event->sender;
            $variable->set("litespeed", LiteSpeedVariable::class);
        });
    }

    private function _handleCraftEvent(Event $event): void
    {
        if (!isset($event->element)) {
            return;
        }

        /** @var Element $element */
        $element = $event->element;

        if (!$element->enabled) {
            return;
        }

        if (ElementHelper::isDraftOrRevision($element)) {
            return;
        }

        LiteSpeed::$plugin->cache->clearAll();
    }
}
