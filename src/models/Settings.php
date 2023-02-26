<?php

namespace furbo\rentmanforcraft\models;

use Craft;
use craft\base\Model;

/**
 * Rentman for Craft settings
 */
class Settings extends Model
{

    /**
     * Some field model attribute
     *
     * @var string
     */
    public $cpTitle = 'Rentman';

    public $apiUrl = 'https://api.rentman.net/';

    public $apiKey = '';

    public $productRoutes = [];

    public $categoryRoutes = [];

    public $projectRoutes = [];

}
