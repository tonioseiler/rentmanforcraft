<?php

namespace furbo\rentmanforcraft\services;

use Craft;
use furbo\rentmanforcraft\elements\Category;
use furbo\rentmanforcraft\elements\Product;
use furbo\rentmanforcraft\RentmanForCraft;
use yii\base\Component;

use GuzzleHttp\Client;

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
        //try {

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
                            ->where(['rentmanId' => $rentmanId])
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
            $products = Product::find()->select(['id', 'rentmanId'])->all();
            
            foreach ($products as $product) {
                if (in_array($product->rentmanId, $rentmanProductIds) === false) {
                    $success = Craft::$app->elements->deleteElement($product);
                }
            }

        /*} catch (RequestException $e) {
            Log::info($e->getRequest() . "\n");
            if ($e->hasResponse()) {
                Log::info($e->getResponse() . "\n");
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
        } */

    }

    public function updateCategories() {

        Cache::flush();

        //start with root category (id = 0)
        if (empty($this->client)) {
            $this->init();
        }
        try {

            $response = $this->client->request('GET', $this->apiUrl.'folders?itemtype=equipment', [
                'headers' => $this->requestHeaders
            ]);

            $jsonResponse = json_decode($response->getBody()->getContents(), true);

            $rentmanCategories = $jsonResponse['data'];


            //loop over folders / categories
            foreach ($rentmanCategories as $id => $rentmanCat) {

                //Category
                $categoryData = [
                     'rentmanId' => $rentmanCat['id'],
                     'sort' => $rentmanCat['order'],
                     'in_shop' => true,
                     'rentman_created' =>  $rentmanCat['created'],
                     'rentman_modified' =>  $rentmanCat['modified'],
                     'name' => Str::title($rentmanCat['displayname']),
                     'slug' => Str::slug($rentmanCat['displayname'])
                ];

                if (empty($rentmanCat['parent'])) {
                    $categoryData['parentId'] = 0;
                } else {
                    $tmp = explode('/', $rentmanCat['parent']);
                    $tmpId = array_pop($tmp);

                    $parent = Category::where('rentmanId', '=', $tmpId)->withTrashed();

                    if ($parent->exists()) {
                        $categoryData['parentId'] = $parent->first()->id;
                    } else {
                        $categoryData['parentId'] = 0;
                    }
                }

                $category = Category::where('rentmanId', '=', $categoryData['rentmanId'])->withTrashed();
                if ($category->exists()) {
                    $category = $category->first();

                    if ($category->trashed()) {
                        $category->restore();
                    }

                    $category->update($categoryData);

                } else {

                    $category = Category::create($categoryData);
                    $category->save();

                    //check if category with the same slug already exists
                    $slug = Str::slug($category->name);
                    $count = 1;
                    $tmp = $this->getCategoryBySlug($slug);
                    while (!empty($tmp)) {
                        $slug = $slug.'-'.$count;
                        $count++;
                        $tmp = $this->getCategoryBySlug($slug);
                    }

                    $category->slug = $slug;
                    $category->save();
                }

            }
            //check if categories were deleted
            $rentmanCategoryIds = Arr::pluck($rentmanCategories, 'id');
            $categories = Category::select('id', 'rentmanId')->whereNull('deleted_at')->get();
            foreach ($categories as $category) {
                if (in_array($category->rentmanId, $rentmanCategoryIds) === false) {
                    $category->delete();
                }
            }
        } catch (RequestException $e) {
            Log::info($e->getRequest() . "\n");
            if ($e->hasResponse()) {
                Log::info($e->getResponse() . "\n");
            }
        }

        Cache::flush();

    }

    public function getDaysFactor($days, $productId) {
        $factors = ['1' => 1,
            '2' => 1.9,
            '3' => 2.7,
            '4' => 3.4,
            '5' => 4,
            '6' => 5,
            '7' => 6,
            '8' => 7,
            '9' => 7.5,
            '10' => 8,
            '11' => 8.5,
            '12' => 9,
            '13' => 9.5,
            '14' => 10,
            '15' => 10.5,
            '16' => 11,
            '17' => 11.5,
            '18' => 12,
            '19' => 12.5,
            '20' => 13,
            '21' => 13.5,
            '22' => 14,
            '23' => 14.5,
            '24' => 15,
            '25' => 15.5,
            '26' => 16,
            '27' => 16.5,
            '28' => 17,
            '29' => 17.5,
            '30' => 18,
            '31' => 18.5,
            '32' => 19,
            '33' => 19.5,
            '34' => 20,
            '35' => 20.5,
            '36' => 21,
            '37' => 21.5,
            '38' => 22,
            '39' => 22.5,
            '40' => 23,
            '41' => 23.5,
            '42' => 24,
            '43' => 24.5,
            '44' => 25,
            '45' => 25.5,
            '46' => 26,
            '47' => 26.5,
            '48' => 27,
            '49' => 27.5,
            '50' => 28,
            '51' => 28.5,
            '52' => 29,
            '53' => 29.5,
            '54' => 30,
            '55' => 30.5,
            '56' => 31,
            '57' => 31.5,
            '58' => 32,
            '59' => 32.5,
            '60' => 33,
            '61' => 33.5,
            '62' => 34,
            '63' => 34.5,
            '64' => 35,
            '65' => 35.5,
            '66' => 36,
            '67' => 36.25,
            '68' => 36.5,
            '69' => 36.75,
            '70' => 37,
            '71' => 37.25,
            '72' => 37.5,
            '73' => 37.75,
            '74' => 38,
            '75' => 38.25,
            '76' => 38.5,
            '77' => 38.75,
            '78' => 39,
            '79' => 39.25,
            '80' => 39.5,
            '81' => 39.75,
            '82' => 40,
            '83' => 40.25,
            '84' => 40.5,
            '85' => 40.75,
            '86' => 41,
            '87' => 41.25,
            '88' => 41.5,
            '89' => 41.75,
            '90' => 42,
            '91' => 42.25,
            '92' => 42.5,
            '93' => 42.75,
            '94' => 43,
            '95' => 43.25,
            '96' => 43.5,
            '97' => 43.75,
            '98' => 44,
            '99' => 44.25,
            '100' => 44.5,
            '101' => 44.75,
            '102' => 45,
            '103' => 45.25,
            '104' => 45.5,
            '105' => 45.75,
            '106' => 46,
            '107' => 46.25
        ];

        if (isset($factors[$days]))
            return $factors[$days];

        return 1;
    }

    public function getAccessoires($productId) {
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


    public function transmitOrder($order, $clientId = null) {
        if (empty($this->client)) {
            $this->init();
        }
        try {

            $remark = "Created on Website";
            if (!empty($order->comment)) {
                $remark .= "<br />".$order->comment;
            }

            if (!empty($order->shooting_days)) {
                $remark .= "<br />".__('app.cart.project.shooting_days').': '.$order->shooting_days;
            }

            if (!empty($order->shooting_locations)) {
                $remark .= "<br />".__('app.cart.project.shooting_locations').': '.$order->shooting_locations;
            }

            if (!empty($order->required_transport)) {
                $remark .= "<br />Fahrzeug: ".$order->required_transport;
            }

            $data = [
                'contact_mailing_number' => $order->delivery_phone,
                'contact_mailing_country' => 'CH',
                'contact_name' => $order->delivery_firstname.' '.$order->delivery_lastname,
                'contact_mailing_postalcode' => $order->billing_zip,
                'contact_mailing_city' => $order->billing_city,
                'contact_mailing_street' => $order->billing_street.' '.$order->billing_street_nr,
                'contact_person_lastname' => $order->delivery_lastname,
                'contact_person_email' => $order->delivery_email,
                'contact_person_middle_name' => '',
                'contact_person_first_name' => $order->delivery_firstname,
                'usageperiod_end' => $this->formatDateTime($order->return_date),
                'usageperiod_start' => $this->formatDateTime($order->pickup_date),
                'in' => $this->formatDateTime($order->return_date),
                'out' => $this->formatDateTime($order->pickup_date),
                'location_mailing_number' => '',//$order->billing_phone,
                'location_mailing_country' => '',//$order->billing_country,
                'location_name' => '',//$order->billing_firstname.' '.$order->billing_lastname,
                'location_mailing_postalcode' => '',//$order->billing_zip,
                'location_mailing_city' => '',//$order->billing_city,
                'location_mailing_street' => '',//$order->billing_street.' '.$order->billing_street_nr,
                'name' => $order->title,
                'external_reference' => $order->id,
                'remark' => $remark,
                'planperiod_end' => $this->formatDateTime($order->to),
                'planperiod_start' => $this->formatDateTime($order->from),
                'price' => $order->amount
            ];

            $response = $this->client->request('POST', $this->apiUrl.'projectrequests', [
                'headers' => $this->requestHeaders,
                'body'    => json_encode($data)
            ]);

            $jsonResponse = json_decode($response->getBody()->getContents(), true);

            $projectId = $jsonResponse['data']['id'];

            $count = 0;
            foreach ($order->orderItemsByCategory() as $oi) {
                $data = [
                    "quantity" => intval($oi->quantity),
                    "quantity_total" => intval($oi->quantity),
                    "is_comment" => false,
                    "is_kit" => true,
                    "discount" => 0,
                    "name" => $oi->product->name,
                    "external_remark" => "",
                    "unit_price" => floatval($oi->product->rental_price),
                    "factor" => "1",
                    "order" => "".$count
                ];

                $count++;

                $response = $this->client->request('POST', $this->apiUrl.'projectrequests/'.$projectId.'/projectrequestequipment', [
                    'headers' => $this->requestHeaders,
                    'body'    => json_encode($data)
                ]);
            }

            $order->state = Order::TRANSMITTED;
            $order->save();


        } catch (\Exception $e) {
            Log::info($e->getMessage() . "\n");
        }
    }
}
