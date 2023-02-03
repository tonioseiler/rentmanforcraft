<?php

namespace furbo\rentmanforcraft\web\assets\rentmanforcraft;

use Craft;
use craft\web\AssetBundle;

/**
 * Rentman For Craft Site asset bundle
 */
class RentmanForCraftSiteAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/dist';
    public $depends = [];
    public $js = ['site.js'];
    public $css = ['site.css'];
}
