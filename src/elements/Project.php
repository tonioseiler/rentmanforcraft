<?php

namespace furbo\rentmanforcraft\elements;

use Craft;
use craft\elements\User;
use craft\elements\conditions\ElementConditionInterface;
use craft\elements\db\ElementQueryInterface;
use craft\fieldlayoutelements\Html;
use craft\fieldlayoutelements\TextareaField;
use craft\fieldlayoutelements\TextField;
use craft\fieldlayoutelements\TitleField;
use craft\helpers\Cp;
use craft\helpers\UrlHelper;
use craft\models\FieldLayout;
use craft\models\FieldLayoutTab;
use craft\web\CpScreenResponseBehavior;
use craft\web\View;
use yii\web\Response;

use furbo\rentmanforcraft\elements\conditions\ProjectCondition;
use furbo\rentmanforcraft\elements\db\ProjectQuery;
use furbo\rentmanforcraft\records\Project as ProjectRecord;
use furbo\rentmanforcraft\RentmanForCraft;

/**
 * Project element type
 */
class Project extends RentmanElement
{

    public $userId;
    public $contact_mailing_number;
    public $contact_mailing_country;
    public $contact_name;
    public $contact_mailing_postalcode;
    public $contact_mailing_city;
    public $contact_mailing_street;
    public $contact_person_lastname;
    public $contact_person_email;
    public $contact_person_middle_name;
    public $contact_person_first_name;
    public $usageperiod_end;
    public $usageperiod_start;
    public $is_paid;
    public $in;
    public $out;
    public $location_mailing_number;
    public $location_mailing_country;
    public $location_name;
    public $location_mailing_postalcode;
    public $location_mailing_city;
    public $location_mailing_street;
    public $external_referenc;
    public $remark;
    public $planperiod_end;
    public $planperiod_start;
    public $price;
    public $shooting_days;
    public $dateOrdered;
    public $dateSubmitted;

    public static function displayName(): string
    {
        return Craft::t('rentman-for-craft', 'Project');
    }

    public static function lowerDisplayName(): string
    {
        return Craft::t('rentman-for-craft', 'project');
    }

    public static function pluralDisplayName(): string
    {
        return Craft::t('rentman-for-craft', 'Projects');
    }

    public static function pluralLowerDisplayName(): string
    {
        return Craft::t('rentman-for-craft', 'projects');
    }

    public static function refHandle(): ?string
    {
        return 'project';
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

    public static function statuses(): array
    {
        return [
            'draft' => ['label' => \Craft::t('rentman-for-craft', 'Draft'), 'color' => 'project-status-0'],
            'ordered' => ['label' => \Craft::t('rentman-for-craft', 'Ordered'), 'color' => 'project-status-1'],
            'submitted' => ['label' => \Craft::t('rentman-for-craft', 'Submitted'), 'color' => 'project-status-2'],
        ];
    }

    public function getStatus(): ?string
    {
        $ret = 'draft';
        if (!empty($this->dateSubmitted))
            $ret = 'submitted';
        else if (!empty($this->dateOrdered) && empty($this->dateSubmitted))
            $ret = 'ordered';
        
        return $ret;
    }



    public static function find(): ElementQueryInterface
    {
        return Craft::createObject(ProjectQuery::class, [static::class]);
    }

    public static function createCondition(): ElementConditionInterface
    {
        return Craft::createObject(ProjectCondition::class, [static::class]);
    }

    protected static function defineSources(string $context): array
    {
        return [
            [
                'key' => '*',
                'label' => Craft::t('rentman-for-craft', 'All projects'),
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
            'link' => ['label' => Craft::t('app', 'Link'), 'icon' => 'world'],
            'id' => ['label' => Craft::t('app', 'ID')],
            'dateCreated' => ['label' => Craft::t('app', 'Date Created')],
            'dateUpdated' => ['label' => Craft::t('app', 'Date Updated')]
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
        $settings = RentmanForCraft::getInstance()->getSettings()->projectRoutes;
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
        $projectRoutes = RentmanForCraft::getInstance()->getSettings()->projectRoutes;
        return [
            'templates/render', [
                'template' => $projectRoutes[$this->site->handle]['template'],
                'variables' => [
                    'project' => $this
                ],
            ],
        ];
    }

    public function canView(User $user = null): bool
    {
        /*
        if (parent::canView($user)) {
            return true;
        }
        // todo: implement user permissions
        return $user->can('viewProjects');
        */

        $currentUser = Craft::$app->getUser()->getIdentity();
        dd($currentUser);
        if (!$currentUser) {
            return false;
        }

        // Admin can everything
        if ($currentUser->admin) {
            return true;
        }


        // Only allow the owner of the project to view it
        return $currentUser->id == $this->userId;

    }

    public function canSave(User $user): bool
    {
        if (parent::canSave($user)) {
            return true;
        }
        // todo: implement user permissions
        return $user->can('saveProjects');
    }

    public function canDuplicate(User $user): bool
    {
        if (parent::canDuplicate($user)) {
            return true;
        }
        // todo: implement user permissions
        return $user->can('saveProjects');
    }

    public function canDelete(User $user): bool
    {
        if (parent::canSave($user)) {
            return true;
        }
        // todo: implement user permissions
        return $user->can('deleteProjects');
    }

    public function canCreateDrafts(User $user): bool
    {
        return true;
    }

    protected function cpEditUrl(): ?string
    {
        return UrlHelper::cpUrl('rentman-for-craft/projects/' . $this->id);
    }

    public function getPostEditUrl(): ?string
    {
        return UrlHelper::cpUrl('rentman-for-craft/projects');
    }

    public function prepareEditScreen(Response $response, string $containerId): void
    {
        /** @var Response|CpScreenResponseBehavior $response */
        $response->crumbs([
            [
                'label' => self::pluralDisplayName(),
                'url' => UrlHelper::cpUrl('rentman-for-craft/projects'),
            ],
        ]);
    }

    public function getFieldLayout(): ?craft\models\FieldLayout
    {

        //possible elements
        // https://docs.craftcms.com/api/v4/craft-base-fieldlayoutelement.html

        $fieldLayout = new FieldLayout();

        //
        $projectTab = new FieldLayoutTab();
        $projectTab->name = Craft::t('rentman-for-craft', 'Projekt');
        $projectTab->setLayout($fieldLayout);
        $layoutElements = [];
        $layoutElements[] = new TitleField(['label' => Craft::t('rentman-for-craft', 'project.title')]);

        $layoutElements[] = new TextField([
            'attribute' => 'in',
            'label' => 'Abholdatum',
            'readonly' => 'true'
        ]);

        $layoutElements[] = new TextField([
            'attribute' => 'out',
            'label' => 'Rückgabedatum',
            'readonly' => 'true'
        ]);

        $layoutElements[] = new TextField([
            'attribute' => 'planperiod_start',
            'label' => 'Drehbeginn',
            'readonly' => 'true'
        ]);

        $layoutElements[] = new TextField([
            'attribute' => 'planperiod_end',
            'label' => 'Drehende',
            'readonly' => 'true'
        ]);

        $layoutElements[] = new TextField([
            'attribute' => 'shooting_days',
            'label' => 'Drehtage',
            'readonly' => 'true'
        ]);

        $layoutElements[] = new TextareaField([
            'attribute' => 'remark',
            'label' => 'Bemerkungen'
        ]);
        $projectTab->setElements($layoutElements);

        //
        $itemsTab = new FieldLayoutTab();
        $itemsTab->name = Craft::t('rentman-for-craft', 'Produkte');
        $itemsTab->setLayout($fieldLayout);
        $layoutElements = [];
        $layoutElements[] = $this->createHtmlLayoutElement('rentman-for-craft/projects/_items', ['label' => Craft::t('rentman-for-craft', 'project.items'), 'project' => $this]);
        $itemsTab->setElements($layoutElements);

        //
        $contactTab = new FieldLayoutTab();
        $contactTab->name = Craft::t('rentman-for-craft', 'Kontakt');
        $contactTab->setLayout($fieldLayout);
        $layoutElements = [];
        $layoutElements[] = new TextField([
            'attribute' => 'contact_person_first_name',
            'label' => 'Vorname'
        ]);
        $layoutElements[] = new TextField([
            'attribute' => 'contact_person_lastname',
            'label' => 'Nachname'
        ]);
        $layoutElements[] = new TextField([
            'attribute' => 'contact_person_email',
            'label' => 'Email'
        ]);
        $layoutElements[] = new TextField([
            'attribute' => 'contact_mailing_number',
            'label' => 'Telefon'
        ]);
        $layoutElements[] = new TextField([
            'attribute' => 'contact_mailing_street',
            'label' => 'Adresse'
        ]);
        $layoutElements[] = new TextField([
            'attribute' => 'contact_mailing_postalcode',
            'label' => 'PLZ'
        ]);
        $layoutElements[] = new TextField([
            'attribute' => 'contact_mailing_city',
            'label' => 'Stadt'
        ]);
        $layoutElements[] = new TextField([
            'attribute' => 'contact_mailing_country',
            'label' => 'Land'
        ]);
        $contactTab->setElements($layoutElements);



        //
        $locationTab = new FieldLayoutTab();
        $locationTab->name = Craft::t('rentman-for-craft', 'Produktion');
        $locationTab->setLayout($fieldLayout);
        $layoutElements = [];
        $layoutElements[] = new TextField([
            'attribute' => 'location_name',
            'label' => 'Name'
        ]);
        $layoutElements[] = new TextField([
            'attribute' => 'location_mailing_number',
            'label' => 'Telefon'
        ]);
        $layoutElements[] = new TextField([
            'attribute' => 'location_mailing_street',
            'label' => 'Adresse'
        ]);
        $layoutElements[] = new TextField([
            'attribute' => 'location_mailing_postalcode',
            'label' => 'PLZ'
        ]);
        $layoutElements[] = new TextField([
            'attribute' => 'location_mailing_city',
            'label' => 'Stadt'
        ]);
        $layoutElements[] = new TextField([
            'attribute' => 'location_mailing_country',
            'label' => 'Land'
        ]);
        $locationTab->setElements($layoutElements);
        
        $fieldLayout->setTabs([$projectTab, $itemsTab, $contactTab, $locationTab]);
    
        return $fieldLayout;
        
    }

    public function getSidebarHtml(bool $static): string {
        return Craft::$app->view->renderTemplate('rentman-for-craft/projects/_submit-button', ['project' => $this], View::TEMPLATE_MODE_CP);
    }

    public function getMetadata(): array {
        $parent = parent::getMetadata();
        $data = [];
        $data['ID'] = $this->id;
        $data['Status'] = $parent['Status'];
        $user = $this->getUser();
        $data['User'] =  !empty($user) ? $user->name : 'Guest';
        $data['Ordered'] =  $this->dateOrdered;
        $data['Submitted'] =  $this->dateSubmitted;
        $data = array_merge($data, $parent);

        return $data;
    }

    public function afterSave(bool $isNew): void
    {
        if (!$this->propagating) {
            if ($isNew) {
                $record = new ProjectRecord();
                $record->id = $this->id;
            }
            else {
                $record = ProjectRecord::findOne($this->id);
            }

            $record->userId = $this->userId;
            $record->contact_mailing_number = $this->contact_mailing_number;
            $record->contact_mailing_country = $this->contact_mailing_country;
            $record->contact_name = $this->contact_name;
            $record->contact_mailing_postalcode = $this->contact_mailing_postalcode;
            $record->contact_mailing_city = $this->contact_mailing_city;
            $record->contact_mailing_street = $this->contact_mailing_street;
            $record->contact_person_lastname = $this->contact_person_lastname;
            $record->contact_person_email = $this->contact_person_email;
            $record->contact_person_middle_name = $this->contact_person_middle_name;
            $record->contact_person_first_name = $this->contact_person_first_name;
            $record->usageperiod_end = $this->usageperiod_end;
            $record->usageperiod_start = $this->usageperiod_start;
            $record->is_paid = $this->is_paid;
            $record->in = $this->in;
            $record->out = $this->out;
            $record->location_mailing_number = $this->location_mailing_number;
            $record->location_mailing_country = $this->location_mailing_country;
            $record->location_name = $this->location_name;
            $record->location_mailing_postalcode = $this->location_mailing_postalcode;
            $record->location_mailing_city = $this->location_mailing_city;
            $record->location_mailing_street = $this->location_mailing_street;
            $record->external_referenc = $this->external_referenc;
            $record->remark = $this->remark;
            $record->planperiod_end = $this->planperiod_end;
            $record->planperiod_start = $this->planperiod_start;
            $record->price = $this->price;
            $record->shooting_days = $this->shooting_days;
            $record->dateOrdered = $this->dateOrdered;
            $record->dateSubmitted = $this->dateSubmitted;
            
            $tmp = $record->save(false);
        }

        parent::afterSave($isNew);
    }

    public function getRecord() {
        if (empty($this->record)) {
            $this->record = ProjectRecord::findOne($this->id);
        }
        return $this->record;
    }

    public function getItems() {
        $record = $this->getRecord();
        if (empty($record)) return [];
        return $record->getItems();
    }
    public function getItemsGroupedByCategory() {
        $record = $this->getRecord();
        if (empty($record)) return [];
        return $record->getItemsGroupedByCategory();
    }





    public function getUser() {
        $record = $this->getRecord();
        return $record->getUser();
    }

    public function getTotalQuantity() {
        $record = $this->getRecord();
        return $record->getTotalQuantity();
    }

    public function getTotalPrice() {
        $record = $this->getRecord();
        return $record->getTotalPrice();
    }

    public function getTotalWeight() {
        $record = $this->getRecord();
        return $record->getTotalWeight();
    }
}
