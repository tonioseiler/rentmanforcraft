<?php
/**
 * Rentman for Craft plugin for Craft CMS 3.x
 *
 * Automatically Import Rentman Products to Craft. Let visitors create orders. Orders are automatically send to rentman as a project request.
 *
 * @link      https://furbo.ch
 * @copyright Copyright (c) 2022 Furbo GmbH
 */

namespace furbo\rentmanforcraft\assetbundles\rentmanforcraftwidgetwidget;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * RentmanForCraftWidgetWidgetAsset AssetBundle
 *
 * AssetBundle represents a collection of asset files, such as CSS, JS, images.
 *
 * Each asset bundle has a unique name that globally identifies it among all asset bundles used in an application.
 * The name is the [fully qualified class name](http://php.net/manual/en/language.namespaces.rules.php)
 * of the class representing it.
 *
 * An asset bundle can depend on other asset bundles. When registering an asset bundle
 * with a view, all its dependent asset bundles will be automatically registered.
 *
 * http://www.yiiframework.com/doc-2.0/guide-structure-assets.html
 *
 * @author    Furbo GmbH
 * @package   RentmanForCraft
 * @since     1.0.0
 */
class RentmanForCraftWidgetWidgetAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * Initializes the bundle.
     */
    public function init()
    {
        // define the path that your publishable resources live
        $this->sourcePath = "@furbo/rentmanforcraft/assetbundles/rentmanforcraftwidgetwidget/dist";

        // define the dependencies
        $this->depends = [
            CpAsset::class,
        ];

        // define the relative path to CSS/JS files that should be registered with the page
        // when this asset bundle is registered
        $this->js = [
            'js/RentmanForCraftWidget.js',
        ];

        $this->css = [
            'css/RentmanForCraftWidget.css',
        ];

        parent::init();
    }
}
