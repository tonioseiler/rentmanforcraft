<?php

namespace furbo\rentmanforcraft;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterCpNavItemsEvent;
use craft\log\MonologTarget;
use craft\services\Elements;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;
use craft\web\twig\variables\Cp;

use yii\base\Event;

use furbo\rentmanforcraft\elements\Category;
use furbo\rentmanforcraft\elements\Product;
use furbo\rentmanforcraft\elements\Project;
use furbo\rentmanforcraft\models\Settings;
use furbo\rentmanforcraft\services\RentmanService;
use furbo\rentmanforcraft\variables\RentmanForCraftVariable;
use Monolog\Formatter\LineFormatter;
use Psr\Log\LogLevel;

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
    public bool $hasCpSection = true;

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
            $this->registerLogger();
        });
    }

    public function getCpNavItem(): ?array
    {

        $cpNavItem = [];

        $settings = $this->getSettings();

        $cpNavItem['label'] = $settings['cpTitle'];
        $cpNavItem['url'] = 'rentman-for-craft';

        $cpNavItem['subnav'] = [];
        $cpNavItem['subnav']['products'] = ['label' => Craft::t('rentman-for-craft', 'Products'), 'url' => 'rentman-for-craft/products'];
        $cpNavItem['subnav']['categories'] = ['label' => Craft::t('rentman-for-craft', 'Categories'), 'url' => 'rentman-for-craft/categories'];
        $cpNavItem['subnav']['projects'] = ['label' => Craft::t('rentman-for-craft', 'Projects'), 'url' => 'rentman-for-craft/projects'];

        return $cpNavItem;
    }

    protected function registerLogger() {
        // Register a custom log target, keeping the format as simple as possible.
        Craft::getLogger()->dispatcher->targets[] = new MonologTarget([
            'name' => 'rentman-for-craft',
            'categories' => ['rentman-for-craft'],
            'level' => LogLevel::INFO,
            'logContext' => false,
            'allowLineBreaks' => false,
            'formatter' => new LineFormatter(
                format: "%datetime% %message%\n",
                dateFormat: 'Y-m-d H:i:s',
            ),
        ]);
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

    protected function getCpRoutes(): array
    {
        return [
            'rentman-for-craft' => [ 'template' => 'rentman-for-craft/products/_index.twig' ],
            'rentman-for-craft/products' => ['template' => 'rentman-for-craft/products/_index.twig'],
            'rentman-for-craft/products/<elementId:\\d+>' => 'elements/edit',
            'rentman-for-craft/categories' => ['template' => 'rentman-for-craft/categories/_index.twig'],
            'rentman-for-craft/categories/<elementId:\\d+>' => 'elements/edit',
            'rentman-for-craft/projects' => ['template' => 'rentman-for-craft/projects/_index.twig'],
            'rentman-for-craft/projects/<elementId:\\d+>' => 'elements/edit',
            
        ];
    }

    private function attachEventHandlers(): void
    {

        // Register event handlers here ...
        // (see https://craftcms.com/docs/4.x/extend/events.html to get started)
        Event::on(Elements::class, Elements::EVENT_REGISTER_ELEMENT_TYPES, function (RegisterComponentTypesEvent $event) {
            $event->types[] = Product::class;
            $event->types[] = Project::class;
            $event->types[] = Category::class;
        });
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function (RegisterUrlRulesEvent $event) {
            $event->rules = array_merge($event->rules, $this->getCpRoutes());
        });
        

        // Executed after settings are saved
        Event::on(
            Plugin::class,
            Plugin::EVENT_AFTER_SAVE_SETTINGS,
            function (Event $event) {
                if ($event->sender::class == "furbo\\rentmanforcraft\\RentmanForCraft") {
                    
                    //save field layout
                    $fieldsService = Craft::$app->getFields();
                    
                    $fieldLayout1 = $fieldsService->assembleLayoutFromPost('settings');
                    $fieldLayout1->type = Product::class;
                    $fieldsService->saveLayout($fieldLayout1);
                    
                }
            }
        );

        // Register our variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('rentman', RentmanForCraftVariable::class);
            }
        );

    }
}
