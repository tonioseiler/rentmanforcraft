<?php

namespace furbo\rentmanforcraft\services;

use Craft;
use craft\console\User;
use craft\elements\User as ElementsUser;
use craft\helpers\Session;
use craft\web\View;
use furbo\rentmanforcraft\elements\Project;
use furbo\rentmanforcraft\records\ProjectItem;
use furbo\rentmanforcraft\RentmanForCraft;
use yii\base\Component;
use yii\web\IdentityInterface;

use Dompdf\Dompdf;
use Dompdf\Options;

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

    public function getProjectTotals($project) {
        return [
            'totalQuantity' => !empty($project) ? $project->getTotalQuantity() : 0,
            'totalPrice' => !empty($project) ? $project->getTotalPrice() : 0,
            'totalWeight' => !empty($project) ? $project->getTotalWeight() : 0
        ];
    }

    // DOING Paolo
    public function getProjectItemsGroupedByCategory($project) {
        return getItemsGroupedByCategory();
    }



    public function updateProjectItem($item) {
        $project = $item->getProject();
        $product = $item->getProduct();

        $factor = $this->getShootingDaysFactor($project->shooting_days);

        $item->unit_price = $product->price;
        $item->factor = $factor;
        $item->price = $item->unit_price * $item->quantity * $item->factor;
        $item->update();
    }

    public function updateProjectItemsAndPrice($project) {
        $items = $project->getItems();
        foreach($items as $item) {
            $this->updateProjectItem($item);
        }
        $project->price = $project->getTotalPrice();
        //$project->update();  This gives an error
        $success = Craft::$app->elements->saveElement($project);
    }

    public function getShootingDaysFactor($days) {
        $settings = RentmanForCraft::getInstance()->getSettings();
        foreach($settings->shootingDaysFactor as $tmp) {
            if ($tmp['days'] == $days) {
                return $tmp['factor'];
            }
        }
        return 1;
    }

    public function generatePDF(Project $project) {
        //$html = Craft::$app->getView()->renderTemplate('rentman-for-craft/projects/_pdf',['project' => $project]);
        $html = Craft::$app->getView()->renderTemplate('projects/_pdf',['project' => $project]);

        $options = new Options();
        $options->set('isRemoteEnabled', TRUE);
        $options->set('debugKeepTemp', TRUE);
        $options->set('isHtml5ParserEnabled', true);
        //$options->setTempDir();
        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);
        $dompdf->render();
        $output = $dompdf->output();

        //todo: save pdf output and return file name


    }


}
