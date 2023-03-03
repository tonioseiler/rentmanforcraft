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


        return match ($status) {
            'draft' => [
                'and',
                [
                    'rentman-for-craft_projects.dateOrdered' => null,
                    'rentman-for-craft_projects.dateSubmitted' => null,
                ]
            ],
            'ordered' => [
                'and',
                ['rentman-for-craft_projects.dateSubmitted' => null],
                ['not', ['rentman-for-craft_projects.dateOrdered' => null]]
            ],
            'submitted' => [
                'and',
                ['not', ['rentman-for-craft_projects.dateSubmitted' => null]],
                ['not', ['rentman-for-craft_projects.dateOrdered' => null]]
            ],
            default => parent::statusCondition($status),
        };
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
