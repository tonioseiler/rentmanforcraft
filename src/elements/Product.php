<?php

namespace furbo\rentmanforcraft\elements;

use Craft;
use craft\base\Element;
use craft\base\FieldLayoutElement;
use craft\elements\User;
use craft\elements\conditions\ElementConditionInterface;
use craft\elements\db\ElementQueryInterface;
use craft\fieldlayoutelements\Html;
use craft\fieldlayoutelements\Template;
use craft\fieldlayoutelements\TextareaField;
use craft\fieldlayoutelements\TextField;
use craft\fieldlayoutelements\TitleField;
use craft\helpers\UrlHelper;
use craft\models\FieldLayout;
use craft\models\FieldLayoutTab;
use craft\web\CpScreenResponseBehavior;
use yii\web\Response;

use furbo\rentmanforcraft\elements\conditions\ProductCondition;
use furbo\rentmanforcraft\elements\db\ProductQuery;
use furbo\rentmanforcraft\records\Product as ProductRecord;
use furbo\rentmanforcraft\RentmanForCraft;
use phpDocumentor\Reflection\Types\Array_;

/**
 * Product element type
 */
class Product extends Element
{
    public $rentmanId;
    public $custom;
    public $displayname;
    public $categoryId;
    public $code;
    public $internal_remark;
    public $external_remark;
    public $location_in_warehouse;
    public $unit;
    public $in_shop;
    public $surface_article;
    public $shop_description_short;
    public $shop_description_long;
    public $shop_seo_title;
    public $shop_seo_keyword;
    public $shop_seo_description;
    public $shop_featured;
    public $price;
    public $subrental_costs;
    public $critical_stock_level;
    public $type;
    public $rental_sales;
    public $temporary;
    public $in_planner;
    public $in_archive;
    public $stock_management;
    public $taxclass;
    public $list_price;
    public $volume;
    public $packed_per;
    public $height;
    public $width;
    public $length;
    public $weight;
    public $power;
    public $current;
    public $images;
    public $files;
    public $ledger;
    public $defaultValuegroup;
    public $qrcodes;
    public $qrcodes_of_serial_numbers;

    public static function displayName(): string
    {
        return Craft::t('rentman-for-craft', 'Product');
    }

    public static function lowerDisplayName(): string
    {
        return Craft::t('rentman-for-craft', 'product');
    }

    public static function pluralDisplayName(): string
    {
        return Craft::t('rentman-for-craft', 'Products');
    }

    public static function pluralLowerDisplayName(): string
    {
        return Craft::t('rentman-for-craft', 'products');
    }

    public static function refHandle(): ?string
    {
        return 'product';
    }

    public static function trackChanges(): bool
    {
        return true;
    }

    public static function hasContent(): bool
    {
        return true;
    }

    public static function hasTitles(): bool
    {
        return true;
    }

    public static function hasUris(): bool
    {
        return true;
    }

    public static function isLocalized(): bool
    {
        return false;
    }

    public static function hasStatuses(): bool
    {
        return true;
    }

    public function getStatus(): ?string
    {
        if ($this->in_shop) {
            return self::STATUS_ENABLED;
        }
        return self::STATUS_DISABLED;
    }

    public static function find(): ElementQueryInterface
    {
        return Craft::createObject(ProductQuery::class, [static::class]);
    }

    public static function createCondition(): ElementConditionInterface
    {
        return Craft::createObject(ProductCondition::class, [static::class]);
    }

    protected static function defineSources(string $context): array
    {
        return [
            [
                'key' => '*',
                'label' => Craft::t('rentman-for-craft', 'All products'),
            ],
        ];
    }

    protected static function defineActions(string $source): array
    {
        // List any bulk element actions here
        return [];
    }

    protected static function includeSetStatusAction(): bool
    {
        return true;
    }

    protected static function defineSortOptions(): array
    {
        return [
            'title' => Craft::t('app', 'Title'),
            'slug' => Craft::t('app', 'Slug'),
            'uri' => Craft::t('app', 'URI'),
            [
                'label' => Craft::t('app', 'Date Created'),
                'orderBy' => 'elements.dateCreated',
                'attribute' => 'dateCreated',
                'defaultDir' => 'desc',
            ],
            [
                'label' => Craft::t('app', 'Date Updated'),
                'orderBy' => 'elements.dateUpdated',
                'attribute' => 'dateUpdated',
                'defaultDir' => 'desc',
            ],
            [
                'label' => Craft::t('app', 'ID'),
                'orderBy' => 'elements.id',
                'attribute' => 'id',
            ],
            // ...
        ];
    }

    protected static function defineTableAttributes(): array
    {
        return [
            'slug' => ['label' => Craft::t('app', 'Slug')],
            'uri' => ['label' => Craft::t('app', 'URI')],
            'link' => ['label' => Craft::t('app', 'Link'), 'icon' => 'world'],
            'id' => ['label' => Craft::t('app', 'ID')],
            'uid' => ['label' => Craft::t('app', 'UID')],
            'dateCreated' => ['label' => Craft::t('app', 'Date Created')],
            'dateUpdated' => ['label' => Craft::t('app', 'Date Updated')]
            // ...
        ];
    }

    protected static function defineDefaultTableAttributes(string $source): array
    {
        return [
            'link',
            'dateCreated',
            // ...
        ];
    }

    protected function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            // ...
        ]);
    }

    public function getUriFormat(): ?string {
        $settings = RentmanForCraft::getInstance()->getSettings()->productRoutes;
        return $settings[$this->site->handle]['uriFormat'];
    }

    protected function previewTargets(): array
    {
        $previewTargets = [];
        $url = $this->getUrl();
        if ($url) {
            $previewTargets[] = [
                'label' => Craft::t('app', 'Primary {type} page', [
                    'type' => self::lowerDisplayName(),
                ]),
                'url' => $url,
            ];
        }
        return $previewTargets;
    }

    protected function route(): array|string|null
    {
        $productRoutes = RentmanForCraft::getInstance()->getSettings()->productRoutes;
        return [
            'templates/render', [
                'template' => $productRoutes[$this->site->handle]['template'],
                'variables' => [
                    'product' => $this,
                ],
            ],
        ];
    }

    public function canView(User $user): bool
    {
        if (parent::canView($user)) {
            return true;
        }
        // todo: implement user permissions
        return $user->can('viewProducts');
    }

    public function canSave(User $user): bool
    {
        if (parent::canSave($user)) {
            return true;
        }
        // todo: implement user permissions
        return $user->can('saveProducts');
    }

    public function canDuplicate(User $user): bool
    {
        if (parent::canDuplicate($user)) {
            return true;
        }
        // todo: implement user permissions
        return $user->can('saveProducts');
    }

    public function canDelete(User $user): bool
    {
        if (parent::canSave($user)) {
            return true;
        }
        // todo: implement user permissions
        return $user->can('deleteProducts');
    }

    public function canCreateDrafts(User $user): bool
    {
        return true;
    }

    protected function cpEditUrl(): ?string
    {
        return UrlHelper::cpUrl('rentman-for-craft/products/' . $this->id);
    }

    public function getPostEditUrl(): ?string
    {
        return UrlHelper::cpUrl('rentman-for-craf/products');
    }

    public function prepareEditScreen(Response $response, string $containerId): void
    {
        /** @var Response|CpScreenResponseBehavior $response */
        $response->crumbs([
            [
                'label' => self::pluralDisplayName(),
                'url' => UrlHelper::cpUrl('products'),
            ],
        ]);
    }

    public function afterSave(bool $isNew): void
    {
        if (!$this->propagating) {
            if ($isNew) {
                $record = new ProductRecord();
                $record->id = $this->id;
            }
            else {
                $record = ProductRecord::findOne($this->id);
            }

            $record->rentmanId = $this->rentmanId;
            $record->custom = $this->custom;
            $record->displayname = $this->displayname;
            $record->categoryId = $this->categoryId;
            $record->code = $this->code;
            $record->internal_remark = $this->internal_remark;
            $record->external_remark = $this->external_remark;
            $record->location_in_warehouse = $this->location_in_warehouse;
            $record->unit = $this->unit;
            $record->in_shop = $this->in_shop;
            $record->surface_article = $this->surface_article;
            $record->shop_description_short = $this->shop_description_short;
            $record->shop_description_long = $this->shop_description_long;
            $record->shop_seo_title = $this->shop_seo_title;
            $record->shop_seo_keyword = $this->shop_seo_keyword;
            $record->shop_seo_description = $this->shop_seo_description;
            $record->shop_featured = $this->shop_featured;
            $record->price = $this->price;
            $record->subrental_costs = $this->subrental_costs;
            $record->critical_stock_level = $this->critical_stock_level;
            $record->type = $this->type;
            $record->rental_sales = $this->rental_sales;
            $record->temporary = $this->temporary;
            $record->in_planner = $this->in_planner;
            $record->in_archive = $this->in_archive;
            $record->stock_management = $this->stock_management;
            $record->taxclass = $this->taxclass;
            $record->list_price = $this->list_price;
            $record->volume = $this->volume;
            $record->packed_per = $this->packed_per;
            $record->height = $this->height;
            $record->width = $this->width;
            $record->length = $this->length;
            $record->weight = $this->weight;
            $record->power = $this->power;
            $record->current = $this->current;
            $record->images = $this->images;
            $record->files = $this->files;
            $record->ledger = $this->ledger;
            $record->defaultValuegroup = $this->defaultValuegroup;
            $record->qrcodes = $this->qrcodes;
            $record->qrcodes_of_serial_numbers = $this->qrcodes_of_serial_numbers;
            $record->save(false);
        }

        parent::afterSave($isNew);
    }

    public function getFieldLayout(): ?craft\models\FieldLayout
    {

        //possible elements
        // https://docs.craftcms.com/api/v4/craft-base-fieldlayoutelement.html
        
        $layoutElements = [];
        
        $layoutElements[] = $this->createImportedValueLayoutElement('displayname', Craft::t('rentman-for-craft', 'product.displayname'), $this->displayname);
        $layoutElements[] = $this->createImportedValueLayoutElement('rentmanId', Craft::t('rentman-for-craft', 'product.rentmanId'), $this->rentmanId);
        $layoutElements[] = $this->createImportedValueLayoutElement('code', Craft::t('rentman-for-craft', 'product.code'), $this->code);
        $layoutElements[] = $this->createHtmlLayoutElement('rentman-for-craft/_includes/images', ['label' => Craft::t('rentman-for-craft', 'product.images'), 'images' => $this->getImages(), 'id' => 'images']);
        $layoutElements[] = $this->createHtmlLayoutElement('rentman-for-craft/_includes/files', ['label' => Craft::t('rentman-for-craft', 'product.files'), 'files' => $this->getFiles(), 'id' => 'files']);
        $layoutElements[] = $this->createImportedValueLayoutElement('in_shop', Craft::t('rentman-for-craft', 'product.in_shop'), $this->in_shop);
        $layoutElements[] = $this->createImportedValueLayoutElement('categoryId', Craft::t('rentman-for-craft', 'product.categoryId'), $this->categoryId);
        $layoutElements[] = $this->createImportedValueLayoutElement('internal_remark', Craft::t('rentman-for-craft', 'product.internal_remark'), $this->internal_remark);
        $layoutElements[] = $this->createImportedValueLayoutElement('external_remark', Craft::t('rentman-for-craft', 'product.external_remark'), $this->external_remark);
        $layoutElements[] = $this->createImportedValueLayoutElement('location_in_warehouse', Craft::t('rentman-for-craft', 'product.location_in_warehouse'), $this->location_in_warehouse);
        $layoutElements[] = $this->createImportedValueLayoutElement('unit', Craft::t('rentman-for-craft', 'product.unit'), $this->unit);
        $layoutElements[] = $this->createImportedValueLayoutElement('surface_article', Craft::t('rentman-for-craft', 'product.surface_article'), $this->surface_article);
        $layoutElements[] = $this->createImportedValueLayoutElement('shop_description_short', Craft::t('rentman-for-craft', 'product.shop_description_short'), $this->shop_description_short);
        $layoutElements[] = $this->createImportedValueLayoutElement('shop_description_long', Craft::t('rentman-for-craft', 'product.shop_description_long'), $this->shop_description_long);
        $layoutElements[] = $this->createImportedValueLayoutElement('shop_seo_title', Craft::t('rentman-for-craft', 'product.shop_seo_title'), $this->shop_seo_title);
        $layoutElements[] = $this->createImportedValueLayoutElement('shop_seo_keyword', Craft::t('rentman-for-craft', 'product.shop_seo_keyword'), $this->shop_seo_keyword);
        $layoutElements[] = $this->createImportedValueLayoutElement('shop_seo_description', Craft::t('rentman-for-craft', 'product.shop_seo_description'), $this->shop_seo_description);
        $layoutElements[] = $this->createImportedValueLayoutElement('shop_featured', Craft::t('rentman-for-craft', 'product.shop_featured'), $this->shop_featured);
        $layoutElements[] = $this->createImportedValueLayoutElement('price', Craft::t('rentman-for-craft', 'product.price'), $this->price);
        $layoutElements[] = $this->createImportedValueLayoutElement('subrental_costs', Craft::t('rentman-for-craft', 'product.subrental_costs'), $this->subrental_costs);
        $layoutElements[] = $this->createImportedValueLayoutElement('critical_stock_level', Craft::t('rentman-for-craft', 'product.critical_stock_level'), $this->critical_stock_level);
        $layoutElements[] = $this->createImportedValueLayoutElement('type', Craft::t('rentman-for-craft', 'product.type'), $this->type);
        $layoutElements[] = $this->createImportedValueLayoutElement('rental_sales', Craft::t('rentman-for-craft', 'product.rental_sales'), $this->rental_sales);
        $layoutElements[] = $this->createImportedValueLayoutElement('temporary', Craft::t('rentman-for-craft', 'product.temporary'), $this->temporary);
        $layoutElements[] = $this->createImportedValueLayoutElement('in_planner', Craft::t('rentman-for-craft', 'product.in_planner'), $this->in_planner);
        $layoutElements[] = $this->createImportedValueLayoutElement('in_archive', Craft::t('rentman-for-craft', 'product.in_archive'), $this->in_archive);
        $layoutElements[] = $this->createImportedValueLayoutElement('stock_management', Craft::t('rentman-for-craft', 'product.stock_management'), $this->stock_management);
        $layoutElements[] = $this->createImportedValueLayoutElement('taxclass', Craft::t('rentman-for-craft', 'product.taxclass'), $this->taxclass);
        $layoutElements[] = $this->createImportedValueLayoutElement('list_price', Craft::t('rentman-for-craft', 'product.list_price'), $this->list_price);
        $layoutElements[] = $this->createImportedValueLayoutElement('volume', Craft::t('rentman-for-craft', 'product.volume'), $this->volume);
        $layoutElements[] = $this->createImportedValueLayoutElement('packed_per', Craft::t('rentman-for-craft', 'product.packed_per'), $this->packed_per);
        $layoutElements[] = $this->createImportedValueLayoutElement('height', Craft::t('rentman-for-craft', 'product.height'), $this->height);
        $layoutElements[] = $this->createImportedValueLayoutElement('width', Craft::t('rentman-for-craft', 'product.width'), $this->width);
        $layoutElements[] = $this->createImportedValueLayoutElement('length', Craft::t('rentman-for-craft', 'product.length'), $this->length);
        $layoutElements[] = $this->createImportedValueLayoutElement('weight', Craft::t('rentman-for-craft', 'product.weight'), $this->weight);
        $layoutElements[] = $this->createImportedValueLayoutElement('power', Craft::t('rentman-for-craft', 'product.power'), $this->power);
        $layoutElements[] = $this->createImportedValueLayoutElement('current', Craft::t('rentman-for-craft', 'product.current'), $this->current);
        $layoutElements[] = $this->createImportedValueLayoutElement('ledger', Craft::t('rentman-for-craft', 'product.ledger'), $this->ledger);
        $layoutElements[] = $this->createImportedValueLayoutElement('defaultValuegroup', Craft::t('rentman-for-craft', 'product.defaultValuegroup'), $this->defaultValuegroup);
        $layoutElements[] = $this->createImportedValueLayoutElement('qrcodes', Craft::t('rentman-for-craft', 'product.qrcodes'), $this->qrcodes);
        $layoutElements[] = $this->createImportedValueLayoutElement('qrcodes_of_serial_numbers', Craft::t('rentman-for-craft', 'product.qrcodes_of_serial_numbers'), $this->qrcodes_of_serial_numbers);
        $layoutElements[] = $this->createImportedValueLayoutElement('custom', Craft::t('rentman-for-craft', 'product.custom'), json_decode($this->custom));
        
        $fieldLayout = new FieldLayout();
    
        $tab = new FieldLayoutTab();
        $tab->name = Craft::t('rentman-for-craft', 'Products');
        $tab->setLayout($fieldLayout);
        $tab->setElements($layoutElements);

        $customElementFieldLayout = \Craft::$app->fields->getLayoutByType(Product::class);
        $tabs = $customElementFieldLayout->getTabs();

        array_unshift($tabs, $tab);

        $fieldLayout->setTabs($tabs);
    
        return $fieldLayout;
        
    }

    protected function createImportedValueLayoutElement($id, $label, $value): FieldLayoutElement {
        return $this->createHtmlLayoutElement('rentman-for-craft/_includes/imported-value', compact('id', 'label', 'value'));
    }

    protected function createHtmlLayoutElement($template, $vars): FieldLayoutElement {
        return new Html(Craft::$app->view->renderTemplate($template, $vars));
    }

    public function getIsEditable(): bool
    {
        return true;
    }


    public function getImages(): Array {
        return json_decode($this->images, true);
    }

    public function getFiles(): Array {
        return json_decode($this->files, true);
    }

}
