<?php

namespace furbo\rentmanforcraft\elements\db;

use Craft;
use craft\elements\db\ElementQuery;
use craft\helpers\Db;

/**
 * Project query
 */
class ProjectQuery extends ElementQuery
{

    public $userId;

    public function userId($value)
    {
        $this->userId = $value;
        return $this;
    }

    protected function statusCondition(string $status): mixed
    {
        switch ($status) {
            case '2':
                return ['not', ['dateSubmitted' => null]];
            case '1':
                return ['not', ['dateOrdered' => null]];
            default:
                return parent::statusCondition($status);
        }
    }


    protected function beforePrepare(): bool
    {
        $this->joinElementTable('rentman-for-craft_projects');

        $this->query->select([
            'rentman-for-craft_projects.*'
        ]);

        if ($this->userId) {
            $this->subQuery->andWhere(Db::parseParam('rentman-for-craft_projects.userId', $this->userId));
        }

        return parent::beforePrepare();
        
    }
}
