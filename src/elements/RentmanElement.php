<?php

namespace furbo\rentmanforcraft\elements;

use Craft;
use craft\base\Element;
use craft\base\FieldLayoutElement;
use craft\fieldlayoutelements\Html;
use craft\web\View;

use Illuminate\Support\Collection;

/**
 * RentmanElement element type
 */
abstract class RentmanElement extends Element
{

    protected $record = null;

    public abstract function getRecord();

    public function getUiLabel(): string
    {
        $displayname = property_exists($this, 'displayname') ? $this->displayname : null;
        return $displayname ?: ($this->title ?: (static::displayName() . ' ' . $this->id));
    }

    protected function createImportedValueLayoutElement($id, $label, $value): FieldLayoutElement {
        return $this->createHtmlLayoutElement('rentman-for-craft/_includes/show/imported-value', compact('id', 'label', 'value'));
    }
    
    protected function createHtmlLayoutElement($template, $vars): FieldLayoutElement {
        return new Html(Craft::$app->view->renderTemplate($template, $vars, View::TEMPLATE_MODE_CP));
    }
}
