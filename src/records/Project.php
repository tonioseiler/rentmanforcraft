<?php

namespace furbo\rentmanforcraft\records;

use Craft;
use craft\db\ActiveRecord;
use craft\elements\User;

/**
 * Project record
 *
 * @property int $id ID
 * @property int|null $userId User ID
 * @property string|null $contact_mailing_number Contact mailing number
 * @property string|null $contact_mailing_country Contact mailing country
 * @property string|null $contact_name Contact name
 * @property string|null $contact_mailing_postalcode Contact mailing postalcode
 * @property string|null $contact_mailing_city Contact mailing city
 * @property string|null $contact_mailing_street Contact mailing street
 * @property string|null $contact_person_lastname Contact person lastname
 * @property string|null $contact_person_email Contact person email
 * @property string|null $contact_person_middle_name Contact person middle name
 * @property string|null $contact_person_first_name Contact person first name
 * @property string|null $usageperiod_end Usageperiod end
 * @property string|null $usageperiod_start Usageperiod start
 * @property string|null $is_paid Is paid
 * @property string|null $in In
 * @property string|null $out Out
 * @property string|null $location_mailing_number Location mailing number
 * @property string|null $location_mailing_country Location mailing country
 * @property string|null $location_name Location name
 * @property string|null $location_mailing_postalcode Location mailing postalcode
 * @property string|null $location_mailing_city Location mailing city
 * @property string|null $location_mailing_street Location mailing street
 * @property string|null $external_referenc External referenc
 * @property string|null $remark Remark
 * @property string|null $planperiod_end Planperiod end
 * @property string|null $planperiod_start Planperiod start
 * @property float|null $price Price
 * @property int|null $shooting_days Shooting Days
 * @property string $dateOrdered Date ordered
 * @property string $dateSubmitted Date submitted
 * @property string $dateCreated Date created
 * @property string $dateUpdated Date updated
 * @property string $uid Uid
 */
class Project extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%rentman-for-craft_projects}}';
    }

    public function getItems() {
        return $this->hasMany(ProjectItem::class, ['projectId' => 'id'])->all();
    }

    public function getUser() {
        $user = Craft::$app->users->getUserById($this->userId);
        return $user;
    }

    public function getTotalQuantity() {
        $items = $this->getItems();
        $ret = 0;
        foreach($items as $item) {
            $ret += $item->quantity;
        }
        return $ret;
    }

    public function getTotalPrice() {
        $items = $this->getItems();
        $ret = 0;
        foreach($items as $item) {
            $ret += $item->price;
        }
        return $ret;
    }

    public function getTotalWeight() {
        $items = $this->getItems();
        $ret = 0;
        foreach($items as $item) {
            $ret += $item->getProduct()->weight * $item->quantity;
        }
        return round($ret, 1);
    }

    
}
