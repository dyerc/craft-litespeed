<?php

namespace dyerc\litespeed;

use craft\base\Plugin;
use craft\events\ElementEvent;
use craft\events\RegisterCacheOptionsEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\services\Elements;
use craft\utilities\ClearCaches;
use craft\web\UrlManager;
use dyerc\litespeed\services\Cache;
use dyerc\litespeed\services\Csrf;
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

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public string $schemaVersion = "1.0.0";

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->_registerServices();
        $this->_registerEvents();

        $this->csrf->inject();
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

    private function _handleCraftEvent(Event $event): void
    {
        if (!isset($event->element)) {
            return;
        }

        $element = $event->element;

        if ($element->enabled && $element->getEnabledForSite()) {
            LiteSpeed::$plugin->cache->clearAll();
        }
    }
}
