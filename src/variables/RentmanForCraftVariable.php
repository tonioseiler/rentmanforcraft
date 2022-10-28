<?php
/**
 * Rentman for Craft plugin for Craft CMS 3.x
 *
 * Automatically Import Rentman Products to Craft. Let visitors create orders. Orders are automatically send to rentman as a project request.
 *
 * @link      https://furbo.ch
 * @copyright Copyright (c) 2022 Furbo GmbH
 */

namespace furbo\rentmanforcraft\variables;

use furbo\rentmanforcraft\RentmanForCraft;

use Craft;

/**
 * Rentman for Craft Variable
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.rentmanForCraft }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    Furbo GmbH
 * @package   RentmanForCraft
 * @since     1.0.0
 */
class RentmanForCraftVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Whatever you want to output to a Twig template can go into a Variable method.
     * You can have as many variable functions as you want.  From any Twig template,
     * call it like this:
     *
     *     {{ craft.rentmanForCraft.exampleVariable }}
     *
     * Or, if your variable requires parameters from Twig:
     *
     *     {{ craft.rentmanForCraft.exampleVariable(twigValue) }}
     *
     * @param null $optional
     * @return string
     */
    public function exampleVariable($optional = null)
    {
        $result = "And away we go to the Twig template...";
        if ($optional) {
            $result = "I'm feeling optional today...";
        }
        return $result;
    }
}
