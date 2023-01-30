<?php

namespace furbo\rentmanforcraft;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\services\Elements;
use craft\web\UrlManager;
use furbo\rentmanforcraft\elements\Category;
use furbo\rentmanforcraft\elements\Product;
use furbo\rentmanforcraft\elements\Project;
use furbo\rentmanforcraft\models\Settings;
use furbo\rentmanforcraft\services\RentmanService;
use yii\base\Event;

/**
 * Rentman for Craft plugin
 *
 * @method static RentmanForCraft getInstance()
 * @method Settings getSettings()
 * @author Furbo GmbH <support@furbo.ch>
 * @copyright Furbo GmbH
 * @license https://craftcms.github.io/license/ Craft License
 * @property-read RentmanService $rentmanService
 */
class RentmanForCraft extends Plugin
{
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSettings = true;

    public static function config(): array
    {
        return [
            'components' => ['rentmanService' => RentmanService::class],
        ];
    }

    public function init()
    {
        parent::init();

        // Defer most setup tasks until Craft is fully initialized
        Craft::$app->onInit(function() {
            $this->attachEventHandlers();
            // ...
        });
    }

    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(Settings::class);
    }

    protected function settingsHtml(): ?string
    {
        return Craft::$app->view->renderTemplate('rentman-for-craft/_settings.twig', [
            'plugin' => $this,
            'settings' => $this->getSettings(),
        ]);
    }

    private function attachEventHandlers(): void
    {
        // Register event handlers here ...
        // (see https://craftcms.com/docs/4.x/extend/events.html to get started)
        Event::on(Elements::class, Elements::EVENT_REGISTER_ELEMENT_TYPES, function (RegisterComponentTypesEvent $event) {
            $event->types[] = Product::class;
        });
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function (RegisterUrlRulesEvent $event) {
            $event->rules['products'] = ['template' => 'rentman-for-craft/products/_index.twig'];
            $event->rules['products/<elementId:\\d+>'] = 'elements/edit';
        });
        Event::on(Elements::class, Elements::EVENT_REGISTER_ELEMENT_TYPES, function (RegisterComponentTypesEvent $event) {
            $event->types[] = Project::class;
        });
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function (RegisterUrlRulesEvent $event) {
            $event->rules['projects'] = ['template' => 'rentman-for-craft/projects/_index.twig'];
            $event->rules['projects/<elementId:\\d+>'] = 'elements/edit';
        });
        Event::on(Elements::class, Elements::EVENT_REGISTER_ELEMENT_TYPES, function (RegisterComponentTypesEvent $event) {
            $event->types[] = Category::class;
        });
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function (RegisterUrlRulesEvent $event) {
            $event->rules['categories'] = ['template' => 'rentman-for-craft/categories/_index.twig'];
            $event->rules['categories/<elementId:\\d+>'] = 'elements/edit';
        });
    }
}
