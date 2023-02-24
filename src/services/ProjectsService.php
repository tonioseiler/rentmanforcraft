<?php

namespace furbo\rentmanforcraft\services;

use Craft;
use craft\helpers\Session;
use furbo\rentmanforcraft\elements\Project;
use yii\base\Component;

/**
 * Projects Service service
 */
class ProjectsService extends Component
{

    public function getActiveProject(): ?Project {
        $id = Session::get('ACTIVE_PROJECT_ID', 0);

        if (empty($id))
            return null;
        
        $user = Craft::$app->getUser()->getIdentity();
        
        if (empty($user)) {
            $project = Project::find()
                ->userId(0)
                ->id($id)
                ->one();
            return $project;
        } else {
            return Project::find()
                ->userId($user->id)
                ->id($id)
                ->one();
        }
    }

}
