<?php

namespace furbo\rentmanforcraft\migrations;

use Craft;
use craft\db\Migration;

/**
 * m230224_001136_change_column_type_in_projects migration.
 */
class m230224_001136_change_column_type_in_projects extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        $this->alterColumn('{{%rentman-for-craft_projects}}', 'dateOrdered', $this->dateTime()->null());
        $this->alterColumn('{{%rentman-for-craft_projects}}', 'dateSubmitted', $this->dateTime()->null());

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo "m230224_001136_change_column_type_in_projects cannot be reverted.\n";
        return false;
    }
}
