<?php

namespace furbo\rentmanforcraft\elements\db;

use Craft;
use craft\elements\db\ElementQuery;

/**
 * Project query
 */
class ProjectQuery extends ElementQuery
{

    public $sessionId;
    public $userId;

    public function sessionId($value)
    {
        $this->sessionId = $value;
        return $this;
    }

    public function userId($value)
    {
        $this->userId = $value;
        return $this;
    }

    protected function beforePrepare(): bool
    {
        $this->joinElementTable('rentman-for-craft_projects');

        if ($this->sessionId) {
            $this->subQuery->andWhere(Db::parseParam('rentman-for-craft_projects.session_id', $this->sessionId));
        }

        if ($this->userId) {
            $this->subQuery->andWhere(Db::parseParam('rentman-for-craft_projects.userId', $this->userId));
        }

        return parent::beforePrepare();
        
    }
}
