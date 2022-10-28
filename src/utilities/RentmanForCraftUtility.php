<?php
/**
 * Rentman for Craft plugin for Craft CMS 3.x
 *
 * Automatically Import Rentman Products to Craft. Let visitors create orders. Orders are automatically send to rentman as a project request.
 *
 * @link      https://furbo.ch
 * @copyright Copyright (c) 2022 Furbo GmbH
 */

namespace furbo\rentmanforcraft\utilities;

use furbo\rentmanforcraft\RentmanForCraft;
use furbo\rentmanforcraft\assetbundles\rentmanforcraftutilityutility\RentmanForCraftUtilityUtilityAsset;

use Craft;
use craft\base\Utility;

/**
 * Rentman for Craft Utility
 *
 * Utility is the base class for classes representing Control Panel utilities.
 *
 * https://craftcms.com/docs/plugins/utilities
 *
 * @author    Furbo GmbH
 * @package   RentmanForCraft
 * @since     1.0.0
 */
class RentmanForCraftUtility extends Utility
{
    // Static
    // =========================================================================

    /**
     * Returns the display name of this utility.
     *
     * @return string The display name of this utility.
     */
    public static function displayName(): string
    {
        return Craft::t('rentman-for-craft', 'RentmanForCraftUtility');
    }

    /**
     * Returns the utility’s unique identifier.
     *
     * The ID should be in `kebab-case`, as it will be visible in the URL (`admin/utilities/the-handle`).
     *
     * @return string
     */
    public static function id(): string
    {
        return 'rentmanforcraft-rentman-for-craft-utility';
    }

    /**
     * Returns the path to the utility's SVG icon.
     *
     * @return string|null The path to the utility SVG icon
     */
    public static function iconPath()
    {
        return Craft::getAlias("@furbo/rentmanforcraft/assetbundles/rentmanforcraftutilityutility/dist/img/RentmanForCraftUtility-icon.svg");
    }

    /**
     * Returns the number that should be shown in the utility’s nav item badge.
     *
     * If `0` is returned, no badge will be shown
     *
     * @return int
     */
    public static function badgeCount(): int
    {
        return 0;
    }

    /**
     * Returns the utility's content HTML.
     *
     * @return string
     */
    public static function contentHtml(): string
    {
        Craft::$app->getView()->registerAssetBundle(RentmanForCraftUtilityUtilityAsset::class);

        $someVar = 'Have a nice day!';
        return Craft::$app->getView()->renderTemplate(
            'rentman-for-craft/_components/utilities/RentmanForCraftUtility_content',
            [
                'someVar' => $someVar
            ]
        );
    }
}
