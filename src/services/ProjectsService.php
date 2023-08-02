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

    public function generatePDF(Project $project, $stream = true) {

        $plugin = RentmanForCraft::getInstance();
        $settings = $plugin->getSettings();
        $filename = 'BLOW UP rental - Anfrage #'.$project->id.'.pdf';
        // TODO Paolo set this title in blowup website, then put stantard title to something like "Project"
        if(isset($settings['pdfFilename']) && !empty($settings['pdfFilename'])) {
            $filename = $settings['pdfFilename'].' - #'.$project->id.'.pdf';
        }

        $templateToUse = 'rentman-for-craft/pdf/project';
        $customTemplate = $settings['templateForProjectPdf']['default']['template'];
        if($customTemplate != '') {
            //$templateToUse='_views/'.$customTemplate;
            $templateToUse='_views/'.substr($customTemplate, 0,- 5);
        }

        $html = Craft::$app->getView()->renderTemplate($templateToUse,['project' => $project], View::TEMPLATE_MODE_CP);
        
        $options = new Options();
        $options->set('isRemoteEnabled', TRUE);
        $options->set('debugKeepTemp', TRUE);
        $options->set('isHtml5ParserEnabled', true);
        //$options->setTempDir();
        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Parameters
        $x          = 494;
        $y          = 790;
        $text       = "Seite {PAGE_NUM} / {PAGE_COUNT}";
        $font       = $dompdf->getFontMetrics()->get_font('Helvetica', 'normal');
        $size       = 10;
        $color      = array(0,0,0);
        $word_space = 0.0;
        $char_space = 0.0;
        $angle      = 0.0;

        $dompdf->getCanvas()->page_text(
            $x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle
        );
        $dompdf->getCanvas()->page_text(
            56, $y, 'BLOW UP rental - +41 44 501 55 30 - mail@blowup-rental.ch', $font, $size, $color, $word_space, $char_space, $angle
        );


        if ($stream) {
            $dompdf->stream($filename);
            //$dompdf->stream("", array("Attachment" => false)); activate for debug, pdf is displayed in the browser (if browser can handle pdf)
        } else {
            $output = $dompdf->output();
            $storagePath = Craft::getAlias('@storage');
            $filepath = $storagePath.'/projects/'.$filename;
            file_put_contents($filepath, $output);
        }
        return $filepath;
    }


}
