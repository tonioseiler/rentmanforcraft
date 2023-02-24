<?php

namespace furbo\rentmanforcraft\migrations;

use Craft;
use craft\db\Migration;

class Install extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {

        if (!$this->db->tableExists('{{%rentman-for-craft_categories}}')) {
            // create the items table
            $this->createTable('{{%rentman-for-craft_categories}}', [
                'id' => $this->primaryKey(),
                'parentId' => $this->integer()->null(),
                'rentmanId' => $this->integer()->notNull(),
                'displayname' => $this->string()->null(),
                'order' =>  $this->integer()->null(),
                'itemtype' => $this->string()->null(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid()
            ]);

            $this->createIndex(null, '{{%rentman-for-craft_categories}}' , ['rentmanId'], false);

            // give it a foreign key to the elements table
            $this->addForeignKey(
                $this->db->getForeignKeyName(),
                '{{%rentman-for-craft_categories}}',
                'id',
                '{{%elements}}',
                'id',
                'CASCADE',
                null
            );
        }

        if (!$this->db->tableExists('{{%rentman-for-craft_products}}')) {
            // create the items table
            $this->createTable('{{%rentman-for-craft_products}}', [
                'id' => $this->primaryKey(),
                'rentmanId' => $this->integer()->notNull(),
                'custom' => $this->longText()->null(),
                'displayname' => $this->string()->null(),
                'categoryId' => $this->integer()->null(),
                'code' => $this->string()->null(),
                'internal_remark' => $this->text()->null(),
                'external_remark' => $this->text()->null(),
                'location_in_warehouse' => $this->text()->null(),
                'unit' => $this->string()->null(),
                'in_shop' => $this->boolean()->defaultValue(0),
                'surface_article' => $this->boolean()->defaultValue(0),
                'shop_description_short' => $this->text()->null(),
                'shop_description_long' => $this->text()->null(),
                'shop_seo_title' => $this->text()->null(),
                'shop_seo_keyword' => $this->text()->null(),
                'shop_seo_description' => $this->text()->null(),
                'shop_featured' => $this->boolean()->defaultValue(0),
                'price' => $this->double()->defaultValue(0),
                'subrental_costs' => $this->double()->defaultValue(0),
                'critical_stock_level' => $this->integer()->null(),
                'type' => $this->string()->notNull(),
                'rental_sales' => $this->string()->notNull(),
                'temporary' => $this->boolean()->defaultValue(0),
                'in_planner' => $this->boolean()->defaultValue(1),
                'in_archive' => $this->boolean()->defaultValue(0),
                'stock_management' => $this->string()->notNull(),
                'taxclass' => $this->string()->notNull(),
                'list_price' => $this->double()->defaultValue(0),
                'volume' => $this->double()->defaultValue(0),
                'packed_per' => $this->double()->defaultValue(0),
                'height' => $this->double()->defaultValue(0),
                'width' => $this->double()->defaultValue(0),
                'length' => $this->double()->defaultValue(0),
                'weight' => $this->double()->defaultValue(0),
                'power' => $this->double()->defaultValue(0),
                'current' => $this->double()->defaultValue(0),
                'images' => $this->text()->null(),
                'files' => $this->text()->null(),
                'ledger' => $this->string()->null(),
                'defaultValuegroup' => $this->string()->null(),
                'qrcodes' => $this->string()->null(),
                'qrcodes_of_serial_numbers' => $this->text()->null(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid()
            ]);

            $this->createIndex(null, '{{%rentman-for-craft_products}}' , ['rentmanId'], false);

            // give it a foreign key to the elements table
            $this->addForeignKey(
                $this->db->getForeignKeyName(),
                '{{%rentman-for-craft_products}}',
                'id',
                '{{%elements}}',
                'id',
                'CASCADE',
                null
            );
        }

        if (!$this->db->tableExists('{{%rentman-for-craft_projects}}')) {
            // create the items table
            $this->createTable('{{%rentman-for-craft_projects}}', [
                'id' => $this->primaryKey(),
                'sessionId' => $this->integer()->null(),
                'userId' => $this->integer()->null(),
                'contact_mailing_number' => $this->string()->null(),
                'contact_mailing_country' => $this->string()->null(),
                'contact_name' => $this->string()->null(),
                'contact_mailing_postalcode' => $this->string()->null(),
                'contact_mailing_city' => $this->string()->null(),
                'contact_mailing_street' => $this->string()->null(),
                'contact_person_lastname' => $this->string()->null(),
                'contact_person_email' => $this->string()->null(),
                'contact_person_middle_name' => $this->string()->null(),
                'contact_person_first_name' => $this->string()->null(),
                'usageperiod_end' => $this->dateTime()->null(),
                'usageperiod_start' => $this->dateTime()->null(),
                'is_paid' => $this->string()->null(),
                'in' => $this->dateTime()->null(),
                'out' => $this->dateTime()->null(),
                'location_mailing_number' => $this->string()->null(),
                'location_mailing_country' => $this->string()->null(),
                'location_name' => $this->string()->null(),
                'location_mailing_postalcode' => $this->string()->null(),
                'location_mailing_city' => $this->string()->null(),
                'location_mailing_street' => $this->string()->null(),
                'external_referenc' => $this->string()->null(),
                'remark' => $this->longText()->null(),
                'planperiod_end' => $this->dateTime()->null(),
                'planperiod_start' => $this->dateTime()->null(),
                'price' => $this->double()->defaultValue(0),
                'dateOrdered' => $this->dateTime()->null(),
                'dateSubmitted' => $this->dateTime()->null(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid()
            ]);

            // give it a foreign key to the elements table
            $this->addForeignKey(
                $this->db->getForeignKeyName(),
                '{{%rentman-for-craft_projects}}',
                'id',
                '{{%elements}}',
                'id',
                'CASCADE',
                null
            );
        }

        if (!$this->db->tableExists('{{%rentman-for-craft_projectitems}}')) {
            // create the items table
            $this->createTable('{{%rentman-for-craft_projectitems}}', [
                'id' => $this->primaryKey(),
                'projectId' => $this->integer()->notNull(),
                'productId' => $this->integer()->notNull(),
                'factor' => $this->double()->defaultValue(1),
                'quantity' => $this->integer()->defaultValue(1),
                'unit_price' => $this->double()->defaultValue(1),
                'price' => $this->double()->defaultValue(1),
                'itemtype' => $this->string()->null(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid()
            ]);

            $this->addForeignKey(
                $this->db->getForeignKeyName('{{%rentman-for-craft_projects}}', 'projectId'),
                '{{%rentman-for-craft_projectitems}}',
                'projectId',
                '{{%rentman-for-craft_projects}}',
                'id',
            );

            $this->addForeignKey(
                $this->db->getForeignKeyName('{{%rentman-for-craft_products}}', 'productId'),
                '{{%rentman-for-craft_projectitems}}',
                'productId',
                '{{%rentman-for-craft_products}}',
                'id'
            );
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        $this->dropTable('{{%rentman-for-craft_projectitems}}');
        $this->dropTable('{{%rentman-for-craft_projects}}');
        $this->dropTable('{{%rentman-for-craft_products}}');
        $this->dropTable('{{%rentman-for-craft_categories}}');
        return true;
        
    }
}
