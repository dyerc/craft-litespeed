[![Stable Version](https://img.shields.io/packagist/v/dyerc/craft-litespeed?label=stable)](<(https://packagist.org/packages/dyerc/craft-litespeed)>)
[![Total Downloads](https://img.shields.io/packagist/dt/dyerc/craft-litespeed)](https://packagist.org/packages/dyerc/craft-litespeed)

<p align="center" style="margin-bottom: 16px;"><img width="130" src="https://raw.githubusercontent.com/dyerc/craft-litespeed/master/src/icon.svg"></p>

# LiteSpeed Plugin for Craft CMS

A very simple integration with the LiteSpeed web server and LSCache for Craft CMS. The entire LSCache is cleared any time an entry is saved. For a more sophisticated caching solution, try [Blitz](https://putyourlightson.com/plugins/blitz).

## Installation

To install the plugin, search for “LiteSpeed” in the Craft Plugin Store, or install manually using composer.

```shell
composer require dyerc/craft-litespeed
```

Update your `.htaccess` to include a block enabling caching for everywhere except the admin area. The following example will cache all pages for 8 hours (`28800` seconds) with the exception of any `/admin` URLs:

```apacheconf
########## Begin - Litespeed cache
<IfModule LiteSpeed>
  RewriteEngine On
  RewriteCond %{REQUEST_METHOD} ^HEAD|GET$
  RewriteCond %{ORG_REQ_URI} !/admin
  RewriteCond %{ORG_REQ_URI} !/index.php/admin
  RewriteRule .* - [E=Cache-Control:max-age=28800]
</IfModule>
########## End - Litespeed cache
```

If you would like to exclude some other page from cache (let's say, `/mypage.php`), simply add the following line to the existing rewrite conditions:

```apacheconf
RewriteCond %{ORG_REQ_URI} !/mypage.php
```

If you want to cache your site for only 4 hours, you can change the max-age. So, it would be:

```apacheconf
RewriteRule .* - [E=Cache-Control:max-age=14400]
```

#### LSCache Check Tool

There's a simple way to see if a URL is cached by LiteSpeed: the [LSCache Check](https://check.lscache.io/) Tool. Enter the URL you wish to check, and the tool will respond with an easy-to-read Yes or No result.

## CSRF Helpers

Any CSRF values (for example `{{ csrfInput() }}`) will be cached by LiteSpeed, preventing forms from working correctly. There are two built-in options for working around this situation:

Automatically inject a script into every page which looks for CSRF inputs, fetches a valid value asynchronously after page load and substitutes in the new value. Configure the plugin to do this be creating a `config/litespeed.php` file containing:

```php
<?php

return [
  "production" => [
    // Or '*' for all environments
    "injectCsrf" => true,
  ],
];
```

On a manual basis by calling the following twig function.

```twig
{{ craft.litespeed.injectCsrf() }}
```

A JavaScript variable `window.LiteSpeed` will also become available containing the following which can be used by any JavaScript within your site which needs to locate a CSRF param.

```js
> window.LiteSpeed
{
  tokenName: "CRAFT_CSRF_TOKEN",
  tokenValue: "..."
}
```

## Requirements

This plugin requires [Craft CMS](https://craftcms.com/) 4.0.0 or later.

---

Created by [Chris Dyer](https://cdyer.co.uk).
