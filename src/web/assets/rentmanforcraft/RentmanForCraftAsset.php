<?php

namespace furbo\rentmanforcraft\web\assets\rentmanforcraft;

use Craft;
use craft\web\AssetBundle;

/**
 * Rentman For Craft asset bundle
 */
class RentmanForCraftAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/dist';
    public $depends = [];
    public $js = [];
    public $css = [];
}
