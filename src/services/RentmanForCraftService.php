<?php
/**
 * Rentman for Craft plugin for Craft CMS 3.x
 *
 * Automatically Import Rentman Products to Craft. Let visitors create orders. Orders are automatically send to rentman as a project request.
 *
 * @link      https://furbo.ch
 * @copyright Copyright (c) 2022 Furbo GmbH
 */

namespace furbo\rentmanforcraft\services;

use furbo\rentmanforcraft\RentmanForCraft;

use Craft;
use craft\base\Component;

/**
 * RentmanForCraftService Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Furbo GmbH
 * @package   RentmanForCraft
 * @since     1.0.0
 */
class RentmanForCraftService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     RentmanForCraft::$plugin->rentmanForCraftService->exampleService()
     *
     * @return mixed
     */
    public function exampleService()
    {
        $result = 'something';
        // Check our Plugin's settings for `someAttribute`
        if (RentmanForCraft::$plugin->getSettings()->someAttribute) {
        }

        return $result;
    }
}
