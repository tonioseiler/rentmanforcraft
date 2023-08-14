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
use craft\web\View;
use yii\web\Response;

use furbo\rentmanforcraft\elements\conditions\ProductCondition;
use furbo\rentmanforcraft\elements\db\ProductQuery;
use furbo\rentmanforcraft\records\Product as ProductRecord;
use furbo\rentmanforcraft\RentmanForCraft;
use phpDocumentor\Reflection\Types\Array_;

/**
 * Product element type
 */
class Product extends RentmanElement
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
        return true;
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
        return false;
    }

    protected static function defineSortOptions(): array
    {
        return [
            'title' => Craft::t('rentman-for-craft', 'Title'),
            'slug' => Craft::t('rentman-for-craft', 'Slug'),
            'uri' => Craft::t('rentman-for-craft', 'URI'),
            [
                'label' => Craft::t('rentman-for-craft', 'Date Created'),
                'orderBy' => 'elements.dateCreated',
                'attribute' => 'dateCreated',
                'defaultDir' => 'desc',
            ],
            [
                'label' => Craft::t('rentman-for-craft', 'Date Updated'),
                'orderBy' => 'elements.dateUpdated',
                'attribute' => 'dateUpdated',
                'defaultDir' => 'desc',
            ]
        ];
    }

    protected static function defineTableAttributes(): array
    {
        return [
            'slug' => ['label' => Craft::t('rentman-for-craft', 'Slug')],
            'link' => ['label' => Craft::t('rentman-for-craft', 'Link'), 'icon' => 'world'],
            'id' => ['label' => Craft::t('rentman-for-craft', 'ID')],
            'images' => ['label' => Craft::t('rentman-for-craft', 'Images')],
            'rentmanId' => ['label' => Craft::t('rentman-for-craft', 'Rentman ID')],
            'files' => ['label' => Craft::t('rentman-for-craft', 'Files')],
            'dateCreated' => ['label' => Craft::t('rentman-for-craft', 'Date Created')],
            'dateUpdated' => ['label' => Craft::t('rentman-for-craft', 'Date Updated')]
            // ...
        ];
    }

    protected static function defineDefaultTableAttributes(string $source): array
    {
        return [
            'images',
            'rentmanId',
            'link',
            'files',
            'dateUpdated'
            // ...
        ];
    }

    public function tableAttributeHtml(string $attribute):string {
        switch ($attribute) {
            case 'images':
                $images = $this->getImages();
                if (count($images) > 0) {
                    $tmp = $this->createHtmlLayoutElement('rentman-for-craft/_includes/index/images', ['images' => $this->getImages()]);
                    return $tmp->formHtml();
                }
                return '';
            case 'files':
                return '';
            
        }
        return parent::tableAttributeHtml($attribute);
        
    }

    protected function defineRules(): array
    {
        return array_merge(parent::defineRules(), [
            // ...
        ]);
    }

    protected static function defineSearchableAttributes(): array
    {
        return [
            'rentmanId',
            'custom',
            'displayname',
            'code',
            'external_remark',
            'shop_description_short',
            'shop_description_long',
            'shop_seo_title',
            'shop_seo_keyword',
            'shop_seo_description'
        ];
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
                'label' => Craft::t('rentman-for-craft', 'Primary {type} page', [
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
        return $user->can('viewProducts');
    }

    public function canSave(User $user): bool
    {
        if (parent::canSave($user)) {
            return true;
        }
        return $user->can('saveProducts');
    }

    public function canDuplicate(User $user): bool
    {
        if (parent::canDuplicate($user)) {
            return true;
        }
        return $user->can('saveProducts');
    }

    public function canDelete(User $user): bool
    {
        if (parent::canSave($user)) {
            return true;
        }
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
        return UrlHelper::cpUrl('rentman-for-craft/products');
    }

    public function prepareEditScreen(Response $response, string $containerId): void
    {
        /** @var Response|CpScreenResponseBehavior $response */
        $response->crumbs([
            [
                'label' => self::pluralDisplayName(),
                'url' => UrlHelper::cpUrl('rentman-for-craft/products'),
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
        
        $layoutElements[] = $this->createImportedValueLayoutElement('title', Craft::t('rentman-for-craft', 'Product name'), $this->title);
        $layoutElements[] = $this->createImportedValueLayoutElement('displayname', Craft::t('rentman-for-craft', 'Display name'), $this->displayname);
        $layoutElements[] = $this->createHtmlLayoutElement('rentman-for-craft/_includes/show/images', ['label' => Craft::t('rentman-for-craft', 'Images'), 'images' => $this->getImages(), 'id' => 'images']);
        $layoutElements[] = $this->createHtmlLayoutElement('rentman-for-craft/_includes/show/files', ['label' => Craft::t('rentman-for-craft', 'Files'), 'files' => $this->getFiles(), 'id' => 'files']);
        $category = $this->getCategory();
        $layoutElements[] = $this->createImportedValueLayoutElement('categoryId', Craft::t('rentman-for-craft', 'Category'), $category->title ?? '–');
        $layoutElements[] = $this->createImportedValueLayoutElement('internal_remark', Craft::t('rentman-for-craft', 'Internal remark'), $this->internal_remark);
        $layoutElements[] = $this->createImportedValueLayoutElement('external_remark', Craft::t('rentman-for-craft', 'External remark'), $this->external_remark);
        $layoutElements[] = $this->createImportedValueLayoutElement('location_in_warehouse', Craft::t('rentman-for-craft', 'Location in warehouse'), $this->location_in_warehouse);
        $layoutElements[] = $this->createImportedValueLayoutElement('unit', Craft::t('rentman-for-craft', 'Unit'), $this->unit);
        $layoutElements[] = $this->createImportedValueLayoutElement('shop_description_short', Craft::t('rentman-for-craft', 'Shop description short'), $this->shop_description_short);
        $layoutElements[] = $this->createImportedValueLayoutElement('shop_description_long', Craft::t('rentman-for-craft', 'Shop description long'), $this->shop_description_long);
        $layoutElements[] = $this->createImportedValueLayoutElement('shop_seo_title', Craft::t('rentman-for-craft', 'Shop seo title'), $this->shop_seo_title);
        $layoutElements[] = $this->createImportedValueLayoutElement('shop_seo_keyword', Craft::t('rentman-for-craft', 'Shop seo keyword'), $this->shop_seo_keyword);
        $layoutElements[] = $this->createImportedValueLayoutElement('shop_seo_description', Craft::t('rentman-for-craft', 'Shop seo description'), $this->shop_seo_description);
        $layoutElements[] = $this->createImportedValueLayoutElement('volume', Craft::t('rentman-for-craft', 'Volume'), $this->volume);
        $layoutElements[] = $this->createImportedValueLayoutElement('packed_per', Craft::t('rentman-for-craft', 'Packed per'), $this->packed_per);
        $layoutElements[] = $this->createImportedValueLayoutElement('height', Craft::t('rentman-for-craft', 'Height'), $this->height);
        $layoutElements[] = $this->createImportedValueLayoutElement('width', Craft::t('rentman-for-craft', 'Width'), $this->width);
        $layoutElements[] = $this->createImportedValueLayoutElement('length', Craft::t('rentman-for-craft', 'Length'), $this->length);
        $layoutElements[] = $this->createImportedValueLayoutElement('weight', Craft::t('rentman-for-craft', 'Weight'), $this->weight);
        $layoutElements[] = $this->createImportedValueLayoutElement('power', Craft::t('rentman-for-craft', 'Power'), $this->power);
        $layoutElements[] = $this->createImportedValueLayoutElement('current', Craft::t('rentman-for-craft', 'Current'), $this->current);
        $layoutElements[] = $this->createImportedValueLayoutElement('ledger', Craft::t('rentman-for-craft', 'Ledger'), $this->ledger);
        $layoutElements[] = $this->createImportedValueLayoutElement('defaultValuegroup', Craft::t('rentman-for-craft', 'Default value group'), $this->defaultValuegroup);
        $layoutElements[] = $this->createImportedValueLayoutElement('custom', Craft::t('rentman-for-craft', 'Custom values'), is_array($this->custom) ? $this->custom : json_decode($this->custom));
        
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

    public function getSidebarHtml(bool $static): string {
        //do not show the status switch
        return '';
    }

    public function getThumbUrl(int $size): ?string {
        return 'thumb url';
    }

    public function getMetadata(): array {
        $parent = parent::getMetadata();
        $data = [];
        $data['ID'] = $this->id;
        $data['Status'] = $parent['Status'];
        $data['Rentman ID'] = $this->rentmanId;
        $data['Code'] = $this->code;
        $data['Featured'] = $this->shop_featured;
        $data['Surface article'] = $this->surface_article;
        $data['Price'] = number_format($this->price, 2);
        $data['Subrental costs'] = number_format($this->subrental_costs, 2);
        $data['List price'] = number_format($this->list_price, 2);
        $data['Critical stock level'] = $this->critical_stock_level;
        $data['Type'] = $this->type;
        $data['Rental / Sales'] = $this->rental_sales;
        $data['Temporary'] = $this->temporary;
        $data['In planner'] = $this->in_planner;
        $data['In archive'] = $this->in_archive;
        $data['Stock management'] = $this->stock_management;
        $data['Taxclass'] = $this->taxclass;
        $data['QR Codes'] = $this->qrcodes;
        $data['QR Codes of serial number'] = $this->qrcodes_of_serial_numbers;
        

        $data['ID'] = $this->id;

        $data = array_merge($data, $parent);

        return $data;
    }


    public function getIsEditable(): bool
    {
        return true;
    }


    public function getImages(): Array {
        $tmp = json_decode($this->images, true);
        if (empty($tmp)) return [];
        return array_filter($tmp, function($img) {
            return $img['in_webshop'] && $img['public'];
        });
    }

    public function getFiles(): Array {
        $tmp = json_decode($this->files, true);
        if (empty($tmp)) return [];
        return array_filter($tmp, function($file) {
            return $file['in_webshop'] && $file['public'];
        });
    }

    public function getCategory() {
        $cat = Category::find()->anyStatus()
                        ->id($this->categoryId)
                        ->one();
        return $cat;
    }

    public function getRecord() {
        if (empty($this->record)) {
            $this->record = ProductRecord::findOne($this->id);
        }
        return $this->record;
    }

}
