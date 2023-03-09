<?php

namespace furbo\rentmanforcraft\controllers;

use Craft;
use craft\elements\User;
use craft\helpers\App;
use craft\helpers\Session;
use craft\web\Controller;
use craft\web\Request;
use DateTime;
use furbo\rentmanforcraft\elements\Product;
use furbo\rentmanforcraft\elements\Project;
use furbo\rentmanforcraft\records\Project as ProjectRecord;
use furbo\rentmanforcraft\records\ProjectItem;
use furbo\rentmanforcraft\RentmanForCraft;
use yii\web\Response;

/**
 * Api controller
 */
class ApiController extends Controller
{
    public $defaultAction = 'index';

    protected array|int|bool $allowAnonymous = [
        'api',
        'products',
        'categories',
        'get-active-project',
        'set-active-project',
        'create-project',
        'update-project',
        'submit-project',
        'set-project-product-quantity',
        'set-project-shooting-days',
        'submit-project',
        'search-products',
        'generate-project-pdf',
    ];

    /**
     * rentman-for-craft/api action
     */
    public function actionIndex(): Response
    {
        return $this->asJson([
            'name' => 'Rentman for craft',
            'version' => '1.0'
        ]);
    }

    /**
     * return all products
     * rentman-for-craft/api/products action
     * 
     * return all products of a category
     * /actions/rentman-for-craft/api/products?categoryId=167
     * 
     * return single product
     * /actions/rentman-for-craft/api/products?id=167
     * 
     */
    public function actionProducts(?int $categoryId = null, ?int $id = null): Response
    {
        if (!empty($id)) {
            $productsService = RentmanForCraft::getInstance()->productsService;
            return $this->asJson($productsService->getProductById($id));
        } else if (!empty($categoryId)) {
            $productsService = RentmanForCraft::getInstance()->productsService;
            return $this->asJson($productsService->getProductsByCategory($categoryId));
        } else {
            App::maxPowerCaptain();
            $productsService = RentmanForCraft::getInstance()->productsService;
            return $this->asJson($productsService->getAllProducts());
        }
    }

    public function actionSearchProducts($query): Response
    {
        $request = Craft::$app->getRequest();
        $productsService = RentmanForCraft::getInstance()->productsService;
        $products = $productsService->searchProducts($query);

        //@paolo: more or less like this
        /*$ret = [];
        foreach($products as $product) {
            $category = $product->getCategory();
            if (!isset($ret[$category->id])) {
                $ret[$category->id] = [];
            }
            $ret[$category->id][] = $product;
        }
        return $this->asJson($ret);*/
        

        return $this->asJson($products);
    }

    /**
     * Return all main categories
     * rentman-for-craft/api/categories
     * 
     * Return all subcategories
     * /actions/rentman-for-craft/api/categories?parentId=183
     * 
     * Return a single category
     * /actions/rentman-for-craft/api/categories?id=183
     * 
     */
    public function actionCategories(?int $parentId = 0, ?int $id = null): Response
    {
        if (!empty($id)) {
            $categoriesService = RentmanForCraft::getInstance()->categoriesService;
            return $this->asJson($categoriesService->getCategoryById($id));
        } else {
            $categoriesService = RentmanForCraft::getInstance()->categoriesService;
            return $this->asJson($categoriesService->getCategories($parentId));
        }
    }

    /**
     * rentman-for-craft/api/projects action
     */
    public function actionGetUserProjects(): Response
    {
        $user = Craft::$app->getUser()->getIdentity();
        $projectService = RentmanForCraft::getInstance()->projectsService;
        $projects = $projectService->getUserProjects($user);
        return $this->asJson($projects);
    }

    /**
     * rentman-for-craft/api/get-active-project action
     */
    public function actionGetActiveProject(): Response
    {
        $projectService = RentmanForCraft::getInstance()->projectsService;
        $project = $projectService->getActiveProject();
        if($project) {
            return $this->asJson($this->createProjectResponse($project));
        } else {
            return $this->asJson(null);
        }
    }

    /**
     * rentman-for-craft/api/set-active-project action
     */
    public function actionSetActiveProject(): Response
    {

        $this->requirePostRequest();
        $request = Craft::$app->getRequest();
        $project = $this->getProjectFromRequest($request);

        Session::set('ACTIVE_PROJECT_ID', $project->id);

        $projectService = RentmanForCraft::getInstance()->projectsService;
        $project = $projectService->getActiveProject();
        
        if ($request->isAjax) {
            return $this->asJson($this->createProjectResponse($project));
        } else {
            return $this->redirectToPostedUrl();
        }
    }

    /**
     * rentman-for-craft/api/set-project-product-quantity action
     * 
     * Should be a post request with csrf token
     * params: productId, quantity (optional)
     * 
     */
    public function actionSetProjectProductQuantity(): Response
    {

        $this->requirePostRequest();
        $request = Craft::$app->getRequest();
        $params = $request->getBodyParams();

        $product = null;
        if (isset($params['productId'])) {
            $product = Product::find()
                ->id($params['productId'])
                ->one();
        }

        $projectId = Session::get('ACTIVE_PROJECT_ID', 0);
        
        $quantity = 1;
        if (isset($params['quantity'])) {
            $quantity = $params['quantity'];
        }

        //check if item exists
        $item = ProjectItem::find()->where(['productId' => $product->id, 'projectId' => $projectId])->one();
        if (empty($item)) {
            $item = new ProjectItem();
            $item->id = 0;
            $item->projectId = $projectId;
            $item->productId = $product->id;
            $item->save();
        }
        $item->quantity = $quantity;
        $item->itemtype = $product->type;
        $item->unit_price = $product->price;
        $item->update();

        if ($quantity <= 0) {
            $item->delete();
        }

        // paolo: added
        if (empty($user)) {
            $project = Project::find()
                ->id($projectId)
                ->one();
        } else {
            $project = Project::find()
                ->id($projectId)
                ->one();
        }

        $projectService = RentmanForCraft::getInstance()->projectsService;
        $projectService->updateProjectItemsAndPrice($project);

        if ($request->isAjax) {
            return $this->asJson($this->createProjectResponse($project));
        } else {
            return $this->redirectToPostedUrl();
        }
    }

    /**
     * rentman-for-craft/api/set-project-shooting-days action
     *
     * Should be a post request with csrf token
     * params: shooting_days
     *
     */
    public function actionSetProjectShootingDays(): Response
    {
        $this->requirePostRequest();
        $request = Craft::$app->getRequest();
        $params = $request->getBodyParams();
        if (isset($params['shooting_days'])) {
            $projectId = Session::get('ACTIVE_PROJECT_ID', 0);
            $user = Craft::$app->getUser()->getIdentity();
            // TODO paolo deal with empty($user), but at this point even the guest should have a session and project id....
            if (empty($user)) {
                $project = Project::find()
                    /*->id($params['projectId'])*/
                    ->id($projectId)
                    ->one();
            } else {
                $project = Project::find()
                    ->id($projectId)
                    ->one();
            }
            foreach($params as $key => $value) {
                if (property_exists($project, $key)) {
                    $project->{$key} = $value;
                }
            }
            $success = Craft::$app->elements->saveElement($project);
            $projectService = RentmanForCraft::getInstance()->projectsService;
            $projectService->updateProjectItemsAndPrice($project);
            if ($request->isAjax) {
                return $this->asJson($this->createProjectResponse($project));
            } else {
                return $this->redirectToPostedUrl();
            }
        }
    }


    /**
     * rentman-for-craft/api/submit-project action
     */
    public function actionSubmitProject(): Response
    {
        $settings = RentmanForCraft::getInstance()->getSettings();
        $rentmanService = RentmanForCraft::getInstance()->rentmanService;

        $this->requirePostRequest();
        $request = Craft::$app->getRequest();
        $project = $this->getProjectFromRequest($request);

        if ($project) {
            $project->dateOrdered = date('Y-m-d H:i:s');

            if ($settings->autoSubmitProjects) {
                $rentmanService->submitProject($project);
                $project->dateSubmitted = date('Y-m-d H:i:s');
            }
            $success = Craft::$app->elements->saveElement($project);
            Session::set('ACTIVE_PROJECT_ID', 0);

            $emailSettings = App::mailSettings();

            Craft::$app
                    ->getMailer()
                    ->composeFromKey('project_ordered', ['project', $project])
                    ->setTo($project->contact_person_email)
                    ->setCc($emailSettings->fromEmail)
                    ->setFrom($emailSettings->fromEmail)
                    ->send();
        }

        if ($request->isAjax) {
            return $this->asJson($this->createProjectResponse($project));
        } else {
            return $this->redirectToPostedUrl();
        }
    }


    //this is to be use from the cp

    /**
     * rentman-for-craft/api/submit-project-to-rentman action
     */
    public function actionSubmitProjectToRentman(): Response
    {

        //TODO: Check if user has cp access

        $settings = RentmanForCraft::getInstance()->getSettings();
        $rentmanService = RentmanForCraft::getInstance()->rentmanService;

        $this->requirePostRequest();
        $request = Craft::$app->getRequest();
        $project = $this->getProjectFromRequest($request);

        if ($project) {
            
            $rentmanService->submitProject($project);
            $project->dateSubmitted = date('Y-m-d H:i:s');
            $success = Craft::$app->elements->saveElement($project);
        }

        if ($request->isAjax) {
            $ret = $this->createProjectResponse($project);
            $ret['message'] = 'Successfully submitted to rentman';
            return $this->asJson($ret);
        } else {
            return $this->redirectToPostedUrl();
        }

    }


    /**
     * rentman-for-craft/api/update-project action
     */
    public function actionUpdateProject(): Response
    {
     
        $this->requirePostRequest();
        $request = Craft::$app->getRequest();
        $params = $request->getBodyParams();
        $project = $this->getProjectFromRequest($request);

        unset($params['id']);

        foreach($params as $key => $value) {
            if (property_exists($project, $key)) {
                $project->{$key} = $value;
            }
        }
        $success = Craft::$app->elements->saveElement($project);

        //update this just in case factor has changed
        $projectService = RentmanForCraft::getInstance()->projectsService;
        $projectService->updateProjectItemsAndPrice($project);
                    
        if ($request->isAjax) {
            return $this->asJson($this->createProjectResponse($project));
        } else {
            return $this->redirectToPostedUrl();
        }
        
    }

    /**
     * rentman-for-craft/api/copy-project action
     */
    public function actionCopyProject(): Response
    {

        $this->requirePostRequest();
        $request = Craft::$app->getRequest();
        $project = $this->getProjectFromRequest($request);

        if ($project) {
            $duplicate = Craft::$app->elements->duplicateElement($project);
            $duplicate->title = $duplicate->title.' (Kopie)';
            $duplicate->dateOrdered = null;
            $duplicate->dateSubmitted = null;
            $success = Craft::$app->elements->saveElement($duplicate);

            //duplicate items
            foreach($project->getItems() as $item) {
                $newItem = new ProjectItem();
                $newItem->id = 0;
                $newItem->projectId = $duplicate->id;
                $newItem->productId = $item->productId;
                $newItem->quantity = $item->quantity;
                $newItem->itemtype = $item->itemtype;
                $newItem->unit_price = $item->unit_price;
                $newItem->save();
            }

            $projectService = RentmanForCraft::getInstance()->projectsService;
            $projectService->updateProjectItemsAndPrice($duplicate);

        }
        if ($request->isAjax) {
            return $this->asJson($this->createProjectResponse($duplicate));
        } else {
            return $this->redirectToPostedUrl();
        }
    }

    /**
     * rentman-for-craft/api/create-project action
     */
    public function actionCreateProject(): Response
    {
        $request = Craft::$app->getRequest();

        $projectService = RentmanForCraft::getInstance()->projectsService;

        $user = $this->getCurrentUser();

        $project = new Project();
        $project->userId = 0;
        $project->title = 'Neues Projekt';
        $project->shooting_days = 1;
        if (!empty($user)) {
            $project->userId = $user->id;
            
            //inherit data from last project
            $lastProject = Project::find()
                ->userId($user->id)
                ->orderBy('id', 'desc')
                ->one();
            if ($lastProject) {
                $project->contact_mailing_number = $lastProject->contact_mailing_number;
                $project->contact_mailing_country = $lastProject->contact_mailing_country;
                $project->contact_name = $lastProject->contact_name;
                $project->contact_mailing_postalcode = $lastProject->contact_mailing_postalcode;
                $project->contact_mailing_city = $lastProject->contact_mailing_city;
                $project->contact_mailing_street = $lastProject->contact_mailing_street;
                $project->contact_person_lastname = $lastProject->contact_person_lastname;
                $project->contact_person_email = $lastProject->contact_person_email;
                $project->contact_person_middle_name = $lastProject->contact_person_middle_name;
                $project->contact_person_first_name = $lastProject->contact_person_first_name;
                $project->location_mailing_number = $lastProject->location_mailing_number;
                $project->location_mailing_country = $lastProject->location_mailing_country;
                $project->location_name = $lastProject->location_name;
                $project->location_mailing_postalcode = $lastProject->location_mailing_postalcode;
                $project->location_mailing_city = $lastProject->location_mailing_city;
                $project->location_mailing_street = $lastProject->location_mailing_street;
            }
            

        }
        $success = Craft::$app->elements->saveElement($project);
        
        if ($request->isAjax) {
            return $this->asJson($this->createProjectResponse($project));
        } else {
            return $this->redirectToPostedUrl();
        }
    }

    /**
     * rentman-for-craft/api/delete-project action
     */
    public function actionDeleteProject(): Response
    {
        $this->requirePostRequest();
        $request = Craft::$app->getRequest();
        $project = $this->getProjectFromRequest($request);

        if ($project) {
            Craft::$app->elements->deleteElement($project);
        }

        if ($request->isAjax) {
            return $this->asJson(['success' => true]);
        } else {
            return $this->redirectToPostedUrl();
        }
    }

    public function actionGenerateProjectPdf(): Response
    {
        $request = Craft::$app->getRequest();
        $project = $this->getProjectFromGetRequest($request);

        $projectService = RentmanForCraft::getInstance()->projectsService;
        $filename = $projectService->generatePDF($project);
        
        return $this->asJson(['success' => true, 'filename' => $filename]);

    }

    private function getCurrentUser(): ?User {
        return Craft::$app->getUser()->getIdentity();
    }

    private function createProjectResponse($project) {
        $projectService = RentmanForCraft::getInstance()->projectsService;

        return [
            'project' => $project,
            'totals' => $projectService->getProjectTotals($project),
            'items' => $project->getItems()
        ];
    }

    private function getProjectFromRequest($request) {
        $params = $request->getBodyParams();
        $user = Craft::$app->getUser()->getIdentity();
        if (empty($user)) {
            return Project::find()
                ->userId(0)
                ->id($params['projectId'])
                ->one();
        } else {
            return Project::find()
                ->userId($user->id)
                ->id($params['projectId'])
                ->one();
        }
    }

    // TODO @Tonio verify this please (it works but maybe it is wrong anyway): I have added this because you told me that the download pdf should be a GET, and I needed to load the project from the projectId get var.
    private function getProjectFromGetRequest($request) {
        $params = $request->getQueryParams();
        $user = Craft::$app->getUser()->getIdentity();
        if (empty($user)) {
            return Project::find()
                ->userId(0)
                ->id($params['projectId'])
                ->one();
        } else {
            return Project::find()
                ->userId($user->id)
                ->id($params['projectId'])
                ->one();
        }
    }


}
