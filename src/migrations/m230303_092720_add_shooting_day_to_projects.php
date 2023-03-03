<?php

namespace furbo\rentmanforcraft\migrations;

use Craft;
use craft\db\Migration;

/**
 * m230303_092720_add_shooting_day_to_projects migration.
 */
class m230303_092720_add_shooting_day_to_projects extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        $this->addColumn('{{%rentman-for-craft_projects}}', 'shooting_days', $this->integer()->defaultValue(1)->after('price'));
        $this->dropColumn('{{%rentman-for-craft_projects}}', 'sessionId');
        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo "m230303_092720_add_shooting_day_to_projects cannot be reverted.\n";
        return false;
    }
}
