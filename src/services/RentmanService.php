<?php

namespace furbo\rentmanforcraft\services;

use Craft;
use furbo\rentmanforcraft\elements\Category;
use furbo\rentmanforcraft\elements\Product;
use furbo\rentmanforcraft\elements\Project;
use furbo\rentmanforcraft\RentmanForCraft;
use yii\base\Component;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;

/**
 * Rentman Service service
 */
class RentmanService extends Component
{

    var $apiUrl = '';
    var $apiKey = '';
    var $client = null;
    var $requestHeaders = [];

    public function init() {

        $plugin = RentmanForCraft::getInstance();
        $settings = $plugin->getSettings();

        $this->apiKey = $settings['apiKey'];
        $this->apiUrl = $settings['apiUrl'];

        $this->requestHeaders = [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept'        => 'application/json',
        ];

        //get API info
        $this->client = new Client(['Content-Type' => 'application/json', 'debug' => false]);


    }

    public function updateProducts() {

        //start with root category (id = 0)
        if (empty($this->client)) {
            $this->init();
        }
        try {

            $hasMoreResults = true;
            $limit = 100;
            $offset = 0;
            $rentmanProductIds = [];

            while ($hasMoreResults) {

                Craft::info("getting products (offset=".$offset.", limit=".$limit.")", 'rentman-for-craft');

                $response = $this->client->request('GET', $this->apiUrl.'equipment', [
                    'headers' => $this->requestHeaders,
                    'query' => [
                        'limit' => $limit,
                        'offset' => $offset
                    ]
                ]);

                $jsonResponse = json_decode($response->getBody()->getContents(), true);

                $offset = $offset + $limit;
                Craft::info($jsonResponse['itemCount']." products found", 'rentman-for-craft');
                if ($jsonResponse['itemCount'] < $limit) {
                    $hasMoreResults = false;
                }

                $rentmanProducts = $jsonResponse['data'];

                //loop over products
                foreach ($rentmanProducts as $rentmanProduct) {

                    $rentmanId = $rentmanProduct['id'];

                    
                    $product = Product::find()
                        ->anyStatus()
                        ->where(['rentmanId' => $rentmanId])
                        ->one();
            
                    if (empty($product)) {
                        //create new
                        $product = new Product();
                    }
                    //update
                    $product->title = $rentmanProduct['name'];
                    $product->rentmanId = $rentmanId;
                    unset($rentmanProduct['id']);
                    foreach($rentmanProduct as $key => $value) {
                        if (property_exists($product, $key)) {
                            $product->{$key} = $value;
                        }
                    }
                    $success = Craft::$app->elements->saveElement($product);
                    
                    //for sets, custom_13 field is filled as weight, so use this as weight
                    /*if ($productData['type'] == 'set' && !empty($rentmanProduct['custom']['custom_13'])) {
                        echo 's';
                        $productData['weight'] = floatval($rentmanProduct['custom']['custom_13']);
                    }*/

                    //assign category
                    $tmp = explode('/', $rentmanProduct['folder']);
                    if (!empty($tmp)) {
                        $tmpId = array_pop($tmp);
                        $category = Category::find()
                            ->where(['rentmanId' => $tmpId])
                            ->one();

                        if ($category) {
                            $product->categoryId = $category->id;
                        } else {
                            $product->categoryId = 0;
                        }
                    } else {
                        $product->categoryId = 0;
                    }
                    $success = Craft::$app->elements->saveElement($product);

                    //get files
                    //https://api.rentman.net/equipment/4566/files
                    $response = $this->client->request('GET', $this->apiUrl.'equipment/'.$product->rentmanId.'/files', [
                        'headers' => $this->requestHeaders
                    ]);

                    $jsonResponse = json_decode($response->getBody()->getContents(), true);
                    $allFiles = $jsonResponse['data'];

                    $files = array_filter($allFiles, function($v, $k) {
                            return !$v['image'];
                        }, ARRAY_FILTER_USE_BOTH);

                    $images = array_filter($allFiles, function($v, $k) {
                        return $v['image'];
                        }, ARRAY_FILTER_USE_BOTH);

                    
                    $product->files = json_encode($files);
                    $product->images = json_encode($images);
                    $success = Craft::$app->elements->saveElement($product);

                    //remeber this for later
                    $rentmanProductIds[] = $product->rentmanId;
                    echo '.';

                }
            }

            //check if products were deleted
            $products = Product::find()->anyStatus()->select(['id', 'rentmanId'])->all();
            
            foreach ($products as $product) {
                if (in_array($product->rentmanId, $rentmanProductIds) === false) {
                    $success = Craft::$app->elements->deleteElement($product);
                    echo 'x';
                }
            }

        } catch (RequestException $e) {
            Craft::error($e->getResponse(), 'rentman-for-craft');
        } catch (\Exception $e) {
            Craft::error($e->getMessage(), 'rentman-for-craft');
        }

    }

    public function updateCategories() {

        //start with root category (id = 0)
        if (empty($this->client)) {
            $this->init();
        }
        //try {

            $response = $this->client->request('GET', $this->apiUrl.'folders?itemtype=equipment', [
                'headers' => $this->requestHeaders
            ]);

            $jsonResponse = json_decode($response->getBody()->getContents(), true);

            $rentmanCategories = $jsonResponse['data'];
            $rentmanCategoryIds = [];

            //loop over folders / categories
            foreach ($rentmanCategories as $id => $rentmanCat) {

                //Category
                $categoryData = [
                     'rentmanId' => $rentmanCat['id'],
                     'order' => $rentmanCat['order'],
                     'displayname' => $rentmanCat['displayname'],
                     'itemtype' => $rentmanCat['itemtype']
                ];

                if (empty($rentmanCat['parent'])) {
                    $categoryData['parentId'] = 0;
                } else {
                    $tmp = explode('/', $rentmanCat['parent']);
                    $tmpId = array_pop($tmp);

                    $parent = Category::find()
                        ->anyStatus()
                        ->where(['rentmanId' => $tmpId])
                        ->one();

                    if ($parent) {
                        $categoryData['parentId'] = $parent->id;
                    } else {
                        $categoryData['parentId'] = 0;
                    }
                }

                $category = $parent = Category::find()
                    ->anyStatus()
                    ->where(['rentmanId' => $rentmanCat['id']])
                    ->one();

                if (empty($category)) {
                    $category = new Category();
                }
                $category->title = $rentmanCat['displayname'];
                foreach($categoryData as $key => $value) {
                    if (property_exists($category, $key)) {
                        $category->{$key} = $value;
                    }
                }
                $success = Craft::$app->elements->saveElement($category);

                $rentmanCategoryIds[] = $category->rentmanId;
                echo '.';
                
            }
            //check if categories were deleted
            $categories = Category::find()->anyStatus()->select(['id', 'rentmanId'])->all();
            
            foreach ($categories as $category) {
                if (in_array($category->rentmanId, $rentmanCategoryIds) === false) {
                    $success = Craft::$app->elements->deleteElement($category);
                    echo 'x';
                }
            }
        /*} catch (RequestException $e) {
            Log::info($e->getRequest() . "\n");
            if ($e->hasResponse()) {
                Log::info($e->getResponse() . "\n");
            }
        }*/

    }

    public function getAccessoires($productId) {
        //TODO: Tonio
        return [];
        return Cache::remember('accessories-'.$productId, 24 * 60, function() use ($productId) {
            if (empty($this->client)) {
                $this->init();
            }
            try {

                //translate id to rentman id
                $product = Product::find($productId);

                $response = $this->client->request('GET', $this->apiUrl.'equipment/'.$product->rentmanId.'/accessories', [
                    'headers' => $this->requestHeaders
                ]);

                $jsonResponse = json_decode($response->getBody()->getContents(), true);

                $equipments = Arr::pluck($jsonResponse['data'], 'equipment');
                $rentmanIds = [];
                foreach ($equipments as $equipment) {
                    $tmp = explode('/',$equipment);
                    $rentmanIds[] = end($tmp);
                }
                $setProducts = Product::whereIn('rentmanId',$rentmanIds)->get();

                return $setProducts;

            } catch (RequestException $e) {
                echo $e->getRequest() . "\n";
                if ($e->hasResponse()) {
                    echo $e->getResponse() . "\n";
                }
            }
        });
    }

    public function getSetContents($productId) {
        //TODO: Tonio
        return [];
        return Cache::remember('set-contents-'.$productId, 24 * 60, function() use ($productId) {
            if (empty($this->client)) {
                $this->init();
            }
            try {

                //translate id to rentman id
                $product = Product::find($productId);

                $response = $this->client->request('GET', $this->apiUrl.'equipment/'.$product->rentmanId.'/equipmentsetscontent', [
                    'headers' => $this->requestHeaders
                ]);

                $jsonResponse = json_decode($response->getBody()->getContents(), true);

                $equipments = Arr::pluck($jsonResponse['data'], 'equipment');
                $rentmanIds = [];
                foreach ($equipments as $equipment) {
                    $tmp = explode('/',$equipment);
                    $rentmanIds[] = end($tmp);
                }
                $setProducts = Product::whereIn('rentmanId',$rentmanIds)->get();

                return $setProducts;

            } catch (RequestException $e) {
                echo $e->getRequest() . "\n";
                if ($e->hasResponse()) {
                    echo $e->getResponse() . "\n";
                }
            }
        });
    }


    public function submitProject(Project $project) {
        
        if (empty($this->client)) {
            $this->init();
        }
       
        $remark = "Created on Website";
        if (!empty($project->remark)) {
            $remark .= "<br />".$project->remark;
        }

        if (!empty($project->shooting_days)) {
            $remark .= '<br />: Drehtage'.$project->shooting_days;
        }

        $data = [
            'contact_mailing_number' => $project->contact_mailing_number,
            'contact_mailing_country' => $project->contact_mailing_country,
            'contact_name' => $project->delivery_firstname.' '.$project->delivery_lastname,
            'contact_mailing_postalcode' => $project->contact_mailing_postalcode,
            'contact_mailing_city' => $project->contact_mailing_city,
            'contact_mailing_street' => $project->contact_mailing_street,
            'contact_person_lastname' => $project->contact_person_lastname,
            'contact_person_email' => $project->contact_person_email,
            'contact_person_middle_name' => $project->contact_person_middle_name,
            'contact_person_first_name' => $project->contact_person_first_name,
            'usageperiod_end' => $this->formatDateTime($project->usageperiod_end),
            'usageperiod_start' => $this->formatDateTime($project->usageperiod_start),
            'in' => $this->formatDateTime($project->in),
            'out' => $this->formatDateTime($project->out),
            'location_mailing_number' => $project->location_mailing_number,
            'location_mailing_country' => $project->location_mailing_country,
            'location_name' => $project->location_name,
            'location_mailing_postalcode' => $project->location_mailing_postalcode,
            'location_mailing_city' => $project->location_mailing_city,
            'location_mailing_street' => $project->location_mailing_street,
            'name' => $project->title,
            'external_reference' => $project->id,
            'remark' => $remark,
            'planperiod_end' => $this->formatDateTime($project->planperiod_end),
            'planperiod_start' => $this->formatDateTime($project->planperiod_start),
            'price' => $project->price
        ];

        $response = $this->client->request('POST', $this->apiUrl.'projectrequests', [
            'headers' => $this->requestHeaders,
            'body'    => json_encode($data)
        ]);

        $jsonResponse = json_decode($response->getBody()->getContents(), true);

        $projectId = $jsonResponse['data']['id'];

        $count = 0;
        foreach ($project->getItems() as $item) {
            $product = $item->getProduct();
            $data = [
                "quantity" => intval($item->quantity),
                "quantity_total" => intval($item->quantity),
                "is_comment" => false,
                "is_kit" => true,
                "discount" => 0,
                "name" => $product->title,
                "external_remark" => "",
                "unit_price" => floatval($item->unit_price),
                "factor" => $item->factor,
                "order" => "".$count
            ];

            $count++;

            $response = $this->client->request('POST', $this->apiUrl.'projectrequests/'.$projectId.'/projectrequestequipment', [
                'headers' => $this->requestHeaders,
                'body'    => json_encode($data)
            ]);
        }

        $project->dateSubmitted = date('Y-m-d H:i:s');
            
    }

    protected function formatDateTime($dateTimeStr) {
        $tmp = new Carbon($dateTimeStr);
        return $tmp->toAtomString();
    }
}
