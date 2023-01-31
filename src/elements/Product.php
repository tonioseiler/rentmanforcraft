<?php

namespace furbo\rentmanforcraft\elements;

use Craft;
use craft\base\Element;
use craft\elements\User;
use craft\elements\conditions\ElementConditionInterface;
use craft\elements\db\ElementQueryInterface;
use craft\helpers\UrlHelper;
use craft\web\CpScreenResponseBehavior;
use yii\web\Response;

use furbo\rentmanforcraft\elements\conditions\ProductCondition;
use furbo\rentmanforcraft\records\Product as ProductRecord;

/**
 * Product element type
 */
class Product extends Element
{
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
            'dateUpdated' => ['label' => Craft::t('app', 'Date Updated')],
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

    public function getUriFormat(): ?string
    {
        // If products should have URLs, define their URI format here
        return null;
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
        // Define how products should be routed when their URLs are requested
        return [
            'templates/render',
            [
                'template' => 'site/template/path',
                'variables' => ['product' => $this],
            ]
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
        return sprintf('products/%s', $this->getCanonicalId());
    }

    public function getPostEditUrl(): ?string
    {
        UrlHelper::cpUrl('products');
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
        return \Craft::$app->fields->getLayoutByType(Product::class);
    }
}
