<?php

namespace furbo\rentmanforcraft\services;

use Craft;
use craft\console\User;
use craft\elements\User as ElementsUser;
use craft\helpers\Session;
use furbo\rentmanforcraft\elements\Project;
use furbo\rentmanforcraft\records\ProjectItem;
use yii\base\Component;
use yii\web\IdentityInterface;

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

    public function getUserProjects($user): array {
        return Project::find()
            ->userId($user->id)
            ->all();
    }

    public function getProjectProductQuantity($productId, $projectId): int {
        $item = ProjectItem::find()
            ->where(['projectId' => $projectId, 'productId' => $productId])
            ->one();
        if (empty($item))
            return 0;

        return $item->quantity;

    }

}
