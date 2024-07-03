<?php

namespace dyerc\litespeed\models;

use craft\base\Model;

class Settings extends Model
{
    /**
     * @var bool Automatically fetch a CSRF token asynchronously and overwrite cached versions on page
     */
    public bool $injectCsrf = false;
}
