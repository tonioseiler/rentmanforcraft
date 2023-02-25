<?php

namespace furbo\rentmanforcraft\records;

use Craft;
use craft\db\ActiveRecord;

/**
 * Rentman record
 *
 */
abstract class ElementRecord extends ActiveRecord
{

    protected $element = null;

    public abstract function getElement();

    public function getUri() {
        return $this->getElement()->uri;   
    }
    
}
