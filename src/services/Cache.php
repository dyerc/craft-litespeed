<?php
/**
 * @copyright Copyright (c) Chris Dyer
 */

namespace dyerc\litespeed\services;

use Craft;
use craft\base\Component;
use craft\helpers\UrlHelper;
use GuzzleHttp\Client;

class Cache extends Component
{
    public function clearAll()
    {
        try {
            $client = $this->guzzleClient();

            $response = $client->get($this->requestClearUrl());

            $decoded = json_decode($response->getBody());

            if ($decoded->status === "ok") {
                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }

    protected function requestClearUrl()
    {
        return UrlHelper::actionUrl("litespeed/cache/clear");
    }

    protected function guzzleClient(): Client
    {
        return Craft::createGuzzleClient([
            "timeout" => 10,
            "connect_timeout" => 5,
            "headers" => [],
        ]);
    }
}
