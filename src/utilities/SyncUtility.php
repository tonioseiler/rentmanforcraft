<?php

namespace furbo\rentmanforcraft\utilities;

use Craft;
use craft\base\Utility;

/**
 * Utilities → Rentman Sync: starts the Rentman sync and shows whether it's running
 */
class SyncUtility extends Utility
{
    public static function displayName(): string
    {
        return Craft::t('rentman-for-craft', 'Rentman Sync');
    }

    public static function id(): string
    {
        return 'rentman-sync';
    }

    public static function icon(): ?string
    {
        return dirname(__DIR__) . '/icon-mask.svg';
    }

    public static function contentHtml(): string
    {
        return Craft::$app->getView()->renderTemplate('rentman-for-craft/utilities/_sync.twig');
    }
}
