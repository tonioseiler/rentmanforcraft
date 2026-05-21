<?php

namespace furbo\rentmanforcraft\migrations;

use Craft;
use craft\db\Migration;

/**
 * Migrates element titles from Craft 4's content table to Craft 5's elements_sites table.
 */
class m260521_000000_migrate_titles_to_elements_sites extends Migration
{
    public function safeUp(): bool
    {
        $tables = [
            '{{%rentman-for-craft_products}}',
            '{{%rentman-for-craft_categories}}',
            '{{%rentman-for-craft_projects}}',
        ];

        foreach ($tables as $table) {
            // Skip if the plugin table doesn't exist yet
            if (!$this->db->tableExists($table)) {
                continue;
            }

            // Skip if the content table is gone (already migrated or fresh install)
            if (!$this->db->tableExists('{{%content}}')) {
                break;
            }

            $this->db->createCommand("
                UPDATE {{%elements_sites}} es
                JOIN {{%content}} c ON c.elementId = es.elementId
                JOIN $table t ON t.id = es.elementId
                SET es.title = c.title
                WHERE es.title IS NULL
                  AND c.title IS NOT NULL
                  AND c.title != ''
            ")->execute();
        }

        return true;
    }

    public function safeDown(): bool
    {
        echo "m260521_000000_migrate_titles_to_elements_sites cannot be reverted.\n";
        return false;
    }
}
