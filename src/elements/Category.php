<?php

namespace furbo\rentmanforcraft\elements;

use Craft;
use craft\base\Element;
use craft\elements\User;
use craft\elements\conditions\ElementConditionInterface;
use craft\elements\db\ElementQueryInterface;
use craft\helpers\UrlHelper;
use craft\models\FieldLayout;
use craft\models\FieldLayoutTab;
use craft\web\CpScreenResponseBehavior;
use yii\web\Response;

use furbo\rentmanforcraft\elements\conditions\CategoryCondition;
use furbo\rentmanforcraft\elements\db\CategoryQuery;
use furbo\rentmanforcraft\records\Category as CategoryRecord;
use furbo\rentmanforcraft\RentmanForCraft;
use Illuminate\Support\Collection;

/**
 * Category element type
 */
class Category extends RentmanElement
{

    public $rentmanId;
    public $parentId;
    public $displayname;
    public $order;
    public $itemtype;

    public static function displayName(): string
    {
        return Craft::t('rentman-for-craft', 'Category');
    }

    public static function lowerDisplayName(): string
    {
        return Craft::t('rentman-for-craft', 'category');
    }

    public static function pluralDisplayName(): string
    {
        return Craft::t('rentman-for-craft', 'Categories');
    }

    public static function pluralLowerDisplayName(): string
    {
        return Craft::t('rentman-for-craft', 'categories');
    }

    public static function refHandle(): ?string
    {
        return 'category';
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

    public static function find(): ElementQueryInterface
    {
        return Craft::createObject(CategoryQuery::class, [static::class]);
    }

    public static function createCondition(): ElementConditionInterface
    {
        return Craft::createObject(CategoryCondition::class, [static::class]);
    }

    protected static function defineSources(string $context): array
    {
        return [
            [
                'key' => '*',
                'label' => Craft::t('rentman-for-craft', 'All categories'),
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
                'label' => Craft::t('app', 'Date Updated'),
                'orderBy' => 'elements.dateUpdated',
                'attribute' => 'dateUpdated',
                'defaultDir' => 'desc',
            ]
        ];
    }

    protected static function defineTableAttributes(): array
    {
        return [
            'slug' => ['label' => Craft::t('app', 'Slug')],
            'link' => ['label' => Craft::t('app', 'Link'), 'icon' => 'world'],
            'id' => ['label' => Craft::t('app', 'ID')],
            'rentmanId' => ['label' => Craft::t('rentman-for-craft', 'Rentman ID')],
            'dateCreated' => ['label' => Craft::t('app', 'Date Created')],
            'dateUpdated' => ['label' => Craft::t('app', 'Date Updated')],
            // ...
        ];
    }

    protected static function defineDefaultTableAttributes(string $source): array
    {
        return [
            'rentmanId',
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
        $settings = RentmanForCraft::getInstance()->getSettings()->categoryRoutes;
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
        $categoryRoutes = RentmanForCraft::getInstance()->getSettings()->categoryRoutes;
        return [
            'templates/render', [
                'template' => $categoryRoutes[$this->site->handle]['template'],
                'variables' => [
                    'category' => $this,
                ],
            ],
        ];
    }

    public function canView(User $user): bool
    {
        if (parent::canView($user)) {
            return true;
        }
        return $user->can('viewCategories');
    }

    public function canSave(User $user): bool
    {
        if (parent::canSave($user)) {
            return true;
        }
        return $user->can('saveCategories');
    }

    public function canDuplicate(User $user): bool
    {
        if (parent::canDuplicate($user)) {
            return true;
        }
        return $user->can('saveCategories');
    }

    public function canDelete(User $user): bool
    {
        if (parent::canSave($user)) {
            return true;
        }
        return $user->can('deleteCategories');
    }

    public function canCreateDrafts(User $user): bool
    {
        return true;
    }

    protected function cpEditUrl(): ?string
    {
        return UrlHelper::cpUrl('rentman-for-craft/categories/' . $this->id);
    }

    public function getPostEditUrl(): ?string
    {
        return UrlHelper::cpUrl('rentman-for-craft/categories');
    }

    public function prepareEditScreen(Response $response, string $containerId): void
    {
        /** @var Response|CpScreenResponseBehavior $response */
        $response->crumbs([
            [
                'label' => self::pluralDisplayName(),
                'url' => UrlHelper::cpUrl('rentman-for-craft/categories'),
            ],
        ]);
    }

    public function afterSave(bool $isNew): void
    {
        if (!$this->propagating) {
            if ($isNew) {
                $record = new CategoryRecord();
                $record->id = $this->id;
            }
            else {
                $record = CategoryRecord::findOne($this->id);
            }

            $record->parentId = $this->parentId;
            $record->rentmanId = $this->rentmanId;
            $record->displayname = $this->displayname;
            $record->order = $this->order;
            $record->itemtype = $this->itemtype;
            $record->save(false);
        }

        parent::afterSave($isNew);

    }

    public function getFieldLayout(): ?craft\models\FieldLayout
    {
        //possible elements
        // https://docs.craftcms.com/api/v4/craft-base-fieldlayoutelement.html
        
        $layoutElements = [];
        
        $layoutElements[] = $this->createImportedValueLayoutElement('title', Craft::t('rentman-for-craft', 'Category name'), $this->title);
        $layoutElements[] = $this->createImportedValueLayoutElement('displayname', Craft::t('rentman-for-craft', 'Display name'), $this->displayname);
        
        $fieldLayout = new FieldLayout();
    
        $tab = new FieldLayoutTab();
        $tab->name = Craft::t('rentman-for-craft', 'Products');
        $tab->setLayout($fieldLayout);
        $tab->setElements($layoutElements);

        $fieldLayout->setTabs([$tab]);
    
        return $fieldLayout;
    }

    public function getMetadata(): array {
        $parent = parent::getMetadata();
        $data = [];
        $data['ID'] = $this->id;
        $data['Rentman ID'] = $this->rentmanId;
        $data['Parent ID'] = $this->parentId;
        $data['Order'] = $this->order;
        
        $data = array_merge($data, $parent);

        return $data;
    }

    public function getIsEditable(): bool
    {
        return true;
    }

    public function getChildren(): ElementQueryInterface|Collection
    {
        return self::find()
                ->parentId($this->id);
    }

    public function hasChildren(): bool
    {
        return $this->getChildren()->count() > 0;
    }

    public function getParent(): ?Category
    {
        return self::find()
                ->id($this->parentId)
                ->one();
    }

    public function isMainCategory():bool
    {
        return $this->parentId == 0;
    }

    public function getRecord() {
        if (empty($this->record)) {
            $this->record = CategoryRecord::findOne($this->id);
        }
        return $this->record;
    }

    

}
