<?php

namespace furbo\rentmanforcraft\records;

use Craft;
use craft\db\ActiveRecord;
use furbo\rentmanforcraft\elements\Product as ProductElement;

/**
 * Product record
 *
 * @property int $id ID
 * @property int $rentmanId Rentman ID
 * @property string|null $custom Custom
 * @property string|null $displayname Displayname
 * @property int|null $category_id Category ID
 * @property string|null $code Code
 * @property string|null $internal_remark Internal remark
 * @property string|null $external_remark External remark
 * @property string|null $location_in_warehouse Location in warehouse
 * @property string|null $unit Unit
 * @property int|null $in_shop In shop
 * @property int|null $surface_article Surface article
 * @property string|null $shop_description_short Shop description short
 * @property string|null $shop_description_long Shop description long
 * @property string|null $shop_seo_title Shop seo title
 * @property string|null $shop_seo_keyword Shop seo keyword
 * @property string|null $shop_seo_description Shop seo description
 * @property int|null $shop_featured Shop featured
 * @property float|null $price Price
 * @property float|null $subrental_costs Subrental costs
 * @property int|null $critical_stock_level Critical stock level
 * @property string $type Type
 * @property string $rental_sales Rental sales
 * @property int|null $temporary Temporary
 * @property int|null $in_planner In planner
 * @property int|null $in_archive In archive
 * @property string $stock_management Stock management
 * @property string $taxclass Taxclass
 * @property float|null $list_price List price
 * @property float|null $volume Volume
 * @property float|null $packed_per Packed per
 * @property float|null $height Height
 * @property float|null $width Width
 * @property float|null $length Length
 * @property float|null $weight Weight
 * @property float|null $power Power
 * @property float|null $current Current
 * @property string|null $images Images
 * @property string|null $files Files
 * @property string|null $ledger Ledger
 * @property string|null $defaultValuegroup Default valuegroup
 * @property string|null $qrcodes Qrcodes
 * @property string|null $qrcodes_of_serial_numbers Qrcodes of serial numbers
 * @property string $dateCreated Date created
 * @property string $dateUpdated Date updated
 * @property string $uid Uid
 */
class Product extends ElementRecord
{
    public static function tableName()
    {
        return '{{%rentman-for-craft_products}}';
    }

    public function getElement() {
        if (empty($this->element)) {
            $this->element = ProductElement::findOne($this->id);
        }
        return $this->element;
    }

    public function getCategory() {
        return $this->hasOne(Category::class, ['id' => 'categoryId'])->one();
    }
}
