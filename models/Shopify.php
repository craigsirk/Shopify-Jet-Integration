<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use linslin\yii2\curl;
use app\models\Products;
use app\models\Jet;
use app\models\jet_Taxonomy;
use app\models\join_JetTaxonomy_ShopifyStores;
/**
 *  shopify model, holds all the shopify API calls
 *
 * @property string $shop
 * @property string $oauth_token
 * @property string $jet_api_key
 * @property string $jet_pass
 * @property integer $created_at
 * @property integer $updated_at
 */
class Shopify extends ActiveRecord
{
    //public $shop;
    //public $oauth_token;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
    	return 'shopify_stores';
    }
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
    	return [
    			TimestampBehavior::className(),
    	];
    }
    public function getJetFulfillmentNodes(){
    	return $this->hasMany(JetFulfillmentNodes::className(), ['ShopifyStore' => 'shop']);
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
        		[['shop', 'oauth_token'], 'required' ]
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'verifyCode' => 'Verification Code',
        ];
    }
    
    
    /**
     * CURL PUT Execute - Not used yet
     * $data should be in an array
     */
    public function executePutShopifyCurl($url, $data, $debug=null){
    	
    	//open connection
    	$ch = curl_init();
    	 
    	//set the url, number of POST vars, POST data
    	curl_setopt($ch,CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_ENCODING, "");
    	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    	curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'X-Shopify-Access-Token: ' .$this->oauth_token,
			'Content-Type: application/json'
		));
    	 
    	//--------------Debug Info -------------------------------------
    	curl_setopt($ch, CURLOPT_VERBOSE, true);
    	$verbose = fopen('php://temp', 'rw+');
    	curl_setopt($ch, CURLOPT_STDERR, $verbose);
    	//-------------End Debug ---------------------------------------
    	 
    	//execute post
    	$result = curl_exec($ch);
    	 
    	rewind($verbose);
    	$verboseLog = stream_get_contents($verbose);
    	 
    	//close connection
    	curl_close($ch);
    	 
    	//If there was an error in the curl, return the verbose
    	if ($result === FALSE) {
    		return false;
	    	Yii::error("cUrl error (#%d): %s<br>\n" . curl_errno($ch). htmlspecialchars(curl_error($ch)) . "<br><br>" . $verboseLog, "shopify.executePutShopifyCurl.error");	    	
    	} else {
    		if ($debug){
    			return $verboseLog;
    		} else {
    			if ($result){
    				return $result;
    			} else {
    				$message = "Success!";
    				return $message;
    			}
    		}
    	}
    }
    
    /**
     * CURL POST Execute
     */
    public function executePostShopifyCurl($url, $data, $type=null, $debug=null){
    	//open connection
		$ch = curl_init();
		
		//set the url, number of GET vars, GET data
		curl_setopt($ch,CURLOPT_URL, $url);
		
		curl_setopt($ch,CURLOPT_POST, 1);
		if ($type=="json"){
			curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'X-Shopify-Access-Token: ' .$this->oauth_token,
					'Content-Type: application/json'
			));
		} else {
			curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($data));
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'X-Shopify-Access-Token: ' .$this->oauth_token,
			));
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		
		//--------------Debug Info -------------------------------------
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		$verbose = fopen('php://temp', 'rw+');
		curl_setopt($ch, CURLOPT_STDERR, $verbose);
		//-------------End Debug ---------------------------------------
		
		//execute post
		$result = curl_exec($ch);
		$result = (json_decode($result, true));
		
		rewind($verbose);
		$verboseLog = stream_get_contents($verbose);
		
    //If there was an error in the curl, return the verbose
    	if ($result === FALSE) {
    		return false;
	    	Yii::error("cUrl error (#%d): %s<br>\n" . curl_errno($ch). htmlspecialchars(curl_error($ch)) . "<br><br>" . $verboseLog, "shopify.executePostShopifyCurl.error");	    	
    	} else {
    		if ($debug){
    			return $verboseLog;
    		} else {
    			if ($result){
    				return $result;
    			} else {
    				$message = "Success!";
    				return $message;
    			}
    		}
    	}
		//close connection
		curl_close($ch);
    }
    
    /**
     * CURL GET Execute
     */
    public function executeGetShopifyCurl($url, $debug=null){
    	//open connection
		$ch = curl_init();
		
		//set the url, number of GET vars, GET data
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'X-Shopify-Access-Token: ' .$this->oauth_token,
			
		));
		
		//--------------Debug Info -------------------------------------
			curl_setopt($ch, CURLOPT_VERBOSE, true);
			$verbose = fopen('php://temp', 'rw+');
			curl_setopt($ch, CURLOPT_STDERR, $verbose);
		//-------------End Debug ---------------------------------------
		
		//execute post
		$result = curl_exec($ch);
		$result = (json_decode($result, true));
		
		rewind($verbose);
		$verboseLog = stream_get_contents($verbose);
		
		//Error in CURL
	    if ($result === FALSE) {
	    	return false;
	    	Yii::error("cUrl error (#%d): %s<br>\n" . curl_errno($ch). htmlspecialchars(curl_error($ch)) . "<br><br>" . $verboseLog, "shopify.executeGetShopifyCurl.error");	    	
	    //debug mode
	    } else if ($debug){
	    	$result['verboseLog']=$verboseLog;
	    	return $result;
	    } else {
	    	return $result;
	    }
	    //close connection
	    curl_close($ch);
    }
    
    /**
     * CURL DELETE Execute
     */
    public function executeDeleteShopifyCurl($url, $debug=null){
    	//open connection
    	$ch = curl_init();
    	
    	//set the url, number of GET vars, GET data
    	curl_setopt($ch,CURLOPT_URL, $url);
    	
    	curl_setopt($ch, CURLOPT_ENCODING, "");
    	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    			'X-Shopify-Access-Token: ' .$this->oauth_token,
    				
    	));
    	
    	//--------------Debug Info -------------------------------------
    	curl_setopt($ch, CURLOPT_VERBOSE, true);
    	$verbose = fopen('php://temp', 'rw+');
    	curl_setopt($ch, CURLOPT_STDERR, $verbose);
    	//-------------End Debug ---------------------------------------
    	
    	//execute post
    	$result = curl_exec($ch);
    	$result = (json_decode($result, true));
    	
    	rewind($verbose);
    	$verboseLog = stream_get_contents($verbose);
    	
    	//Error in CURL
    	if ($result === FALSE) {
	    	Yii::error("cUrl error (#%d): %s<br>\n" . curl_errno($ch). htmlspecialchars(curl_error($ch)) . "<br><br>" . $verboseLog, "shopify.executeDeleteShopifyCurl.error");	    	
    		
    		//debug mode
    	} else if ($debug){
    		$result['verboseLog']=$verboseLog;
    		return $result;
    	
    		//Successful
    	} else {
    		return $result;
    	}
    	//close connection
    	curl_close($ch);
    }
    
    /**
     * Get all products 
     * fields is comma separated list
     */
    public function getAllProducts($fields=null, $debug=null){
    	$url = "https://" . $this->shop . Yii::$app->params['shopify']['products'] . ".json";
    	if ($fields){
    		$url .= "?fields=" . $fields;
    	}
    	$result = $this->executeGetShopifyCurl($url, $debug);
    	return $result;
    }
    
    /**
     * gets all products from shop
     * @return array of products
     */
	public function getProducts($id=null, $debug=null){

		$url = "https://" . $this->shop . Yii::$app->params['shopify']['products'];
		
		if ($id){
			$url .= "/" . $id . ".json";
		} else {
			$url .= ".json";
		}
		
		$result = $this->executeGetShopifyCurl($url);
	    //Successful
	    if ($debug){
	    	return $result;
	    } else {
			//If we are getting a single product, create all the product attributes
			if ($id){
				$products_obj= new Products();
					
			//These values are taken from Shopify Product
				$products_obj->id = $result['product']['id'];
				
				//images
				foreach ($result['product']['images'] as $key=>$image){
					if($key!=0){
						$imgArray[$key-1]['image_slot_id'] = $key;
						$imgArray[$key-1]['image_url']= $result['product']['images'][$key]['src'];
					}
				}

				//Get all metadata for product
				$metadata = $this->getProductMetadata($id, "jet_settings");
				$products_obj->fullArray =$metadata;
				
				//Get the product from the shopify description.  If it has been changed in the Jet
				//products update page, use the meta data instead.
				
				//$products_obj->product_description = $result['product']['product_description']; //REQUIRED
				$products_obj->product_description=$result['product']['body_html'];
				
				foreach ($metadata['metafields'] as $key=>$meta){
					
					if ($products_obj->hasProperty($meta['key'])) {
						if($meta['key'] == "cpsia_cautionary_statements"){
							$tempArray = explode(",", $meta['value']);
							$products_obj->{$meta['key']} = $tempArray;
						} else {
							$products_obj->{$meta['key']} = $meta['value'];
						}
					}
				}
				//Get Jet Taxonomy based on Shopify ProductType
				$node = join_JetTaxonomy_ShopifyStores::find()->where(['ShopifyType'=>$result['product']['product_type']])->andWhere(['shopifyStore'=>$this->shop])->one();
				
				if ($node){
					$products_obj->jet_browse_node_name = $node->jet_Taxonomy->jet_node_name;
					$products_obj->jet_browse_node_id = (int)$node->jet_Taxonomy->jet_node_id;
					$products_obj->jet_tax_code_recommendation = $node->jet_Taxonomy->suggested_tax_code;
				}
				
				$products_obj->product_title = $result['product']['title']; //REQUIRED
				$products_obj->standard_product_code=$result['product']['variants'][0]['barcode'];//REQUIRED
				$products_obj->standard_product_code_type = "UPC";//REQUIRED
				$products_obj->brand=$result['product']['vendor'];//REQUIRED
				$products_obj->main_image_url=$result['product']['images'][0]['src'];//REQUIRED
				$products_obj->shipping_weight_pounds= $result['product']['variants'][0]['weight'];
				$products_obj->sku =$result['product']['variants'][0]['sku'];
				$products_obj->alternate_images = $imgArray;
				return $products_obj;
				
			
			// We are getting all products, only need a select few attributes to be sent back.
			} else {
				$products_array = array();
				foreach ($result['products'] as $key=>$product){
					$products_array[$key]= new Products();
					$products_array[$key]->id = $product['id'];
					$products_array[$key]->product_title = $product['title'];
					$products_array[$key]->vendor = $product['vendor'];
					$products_array[$key]->product_type = $product['product_type'];
					$products_array[$key]->ShopifyPrice= $product['variants'][0]['price'];
					$products_array[$key]->barcode= $product['variants'][0]['barcode'];
					$products_array[$key]->inv_quantity= $product['variants'][0]['inventory_quantity'];
					$products_array[$key]->fullArray =$product;
					$products_array[$key]->sku=$product['variants'][0]['sku'];
					
					//Get all metadata for product
					$metadata = $this->getProductMetadata($product['id'], "jet_settings");
									
					foreach ($metadata['metafields'] as $metaKey=>$meta){
							
						if ($products_array[$key]->hasProperty($meta['key'])) {
							if($meta['key'] == "cpsia_cautionary_statements"){
								$tempArray = explode(",", $meta['value']);
								$products_array[$key]->{$meta['key']} = $tempArray;
							} else {
								$products_array[$key]->{$meta['key']} = $meta['value'];
							}
						}
					}
					if (!$products_array[$key]->uploaded){
						$products_array[$key]->uploaded = "No";
					}
				}	
				return $products_array;
			}	
		}
		//close connection
		curl_close($ch);
	}
	
	/* 
	 * Get product metadata from shopify
	 */
	public function getProductMetadata($id, $namespace=null, $debug=false){
		$url = "https://" . $this->shop . Yii::$app->params['shopify']['products'] . "/" . $id . "/metafields.json";
	
		if ($namespace){
			$url .= "?namespace=" . $namespace;
		}
		$result = $this->executeGetShopifyCurl($url);
		return $result;
	}
	
	/*
	 * Sets the metadata of a shopify product
	 * $namespace should be "jet_settings"
	 * $value_type can be "string" or "integer"
	 */
	public function postProductMetadata($id, $namespace, $key, $value, $value_type, $description=null, $debug=null){
		$url = "https://" . $this->shop . Yii::$app->params['shopify']['products'] . "/" . $id . "/metafields.json";
		
		//setup fields
		$data = array(
			'metafield'=>array( 
				'namespace' => $namespace,
				'key' => $key,
				'value'=>$value,
				'value_type'=> $value_type,
				'description'=>$description
			)
		);
		
		$result = $this->executePostShopifyCurl($url, $data);
		return $result;
		
	}
	
	/*
	 * delete the metadata of a shopify product
	 */
	public function deleteProductMetadata($id, $metaID, $debug=null){
		$url = "https://" . $this->shop . Yii::$app->params['shopify']['products'] . "/" . $id . "/metafields/" . $metaID . ".json";
		$result = $this->executeDeleteShopifyCurl($url);
		return $result;
	}
	
	/**
	 * Post order from Jet to Shopify
	 * @param unknown $data
	 * @param unknown $debug
	 */
	public function postOrder($data, $debug=null){
		$url = "https://" . $this->shop . Yii::$app->params['shopify']['admin'] . "/orders.json";
		$result = $this->executePostShopifyCurl($url, $data, "json", $debug);
		return $result;
	}
	
	/**
	 * @param $fields should be comma separated
	 * @param unknown $status
	 * @param ids is comma separated array
	 */
	public function getOrders($status=null, $ids=null, $fields=null, $debug=null){
		$url = "https://" . $this->shop . Yii::$app->params['shopify']['admin'] . "/orders.json";
		
		if ($status){
			$url .= "?status=" . $status;
		} else {
			$url .= "?status=open";
		}
		if ($ids){
			$url .= "&ids=" . $ids;
		}
		if ($fields){
			$url .= "&fields=" . $fields;
		}
		$result = $this->executeGetShopifyCurl($url, $debug);
		return $result;
	}
	
	/**
	 * 
	 * @param unknown $id
	 * @param unknown $debug
	 */
	public function putOrderUpdate($id, $data, $debug=null){
		$url = "https://" . $this->shop . Yii::$app->params['shopify']['admin'] . "/orders/" . $id . ".json";
		$result = $this->executePutShopifyCurl($url, $data, $debug);
		return $result;
	}
	
	/**
	 * 
	 * @param unknown $id
	 * @param unknown $data
	 * @param unknown $debug
	 */
	public function postOrderRefund($id, $data, $type=null, $debug=null){
		$url = "https://" . $this->shop . Yii::$app->params['shopify']['admin'] . "/orders/" . $id . "/refunds.json";
		$result = $this->executePostShopifyCurl($url, $data, $type, $debug);
		return $result;
	}
	
	/**
	 * 
	 * @param unknown $id
	 * @param unknown $data
	 * @param unknown $debug
	 */
	public function postCancelOrder($id, $data, $debug=null){
		$url = "https://" . $this->shop . Yii::$app->params['shopify']['admin'] . "/orders/" . $id . "/cancel.json";
		$result = $this->executePostShopifyCurl($url, $data, "json", $debug);
		return $result;
	}
	
	/**
	 * 
	 */
	public function postOrderFulfillment($id, $data, $type=null, $debug=null){
		$url = "https://" . $this->shop . Yii::$app->params['shopify']['admin'] . "/orders/" . $id . "/fulfillments.json";
		$result = $this->executePostShopifyCurl($url, $data, $type, $debug);
		return $result;
	}
	
	/**
	 * 
	 * @param unknown $id
	 * @param unknown $debug
	 */
	public function deleteOrder($id, $debug=null){
		$url = "https://" . $this->shop . Yii::$app->params['shopify']['admin'] . "/orders/" . $id . ".json";
		$result = $this->executeDeleteShopifyCurl($url, $debug);
		return $result;
	}
	
	/**
	 * Sets a webhook for shopify
	 * @param unknown $data
	 * @param unknown $debug
	 */
	public function postWebhook($data, $debug=null){
		$url = "https://" . $this->shop . Yii::$app->params['shopify']['admin'] . "/webhooks.json";
		$result = $this->executePostShopifyCurl($url, $data, "json", $debug);
		return $result;
	}
	
	/**
	 * 
	 * @param unknown $data
	 * @param unknown $debug
	 */
	public function deleteWebhook($id,$debug=null){
		$url = "https://" . $this->shop . Yii::$app->params['shopify']['admin'] . "/webhooks/" . $id . ".json";
		$result = $this->executeDeleteShopifyCurl($url, $data, "json", $debug);
		return $result;
	}
	
	/**
	 * 
	 * @param unknown $data
	 * @param unknown $debug
	 */
	public function getWebhook($debug=null){
		$url = "https://" . $this->shop . Yii::$app->params['shopify']['admin'] . "/webhooks.json";
		$result = $this->executeGetShopifyCurl($url, $debug);
		return $result;
	}
	
	public function verify_webhook($debug=null)
	{
		$hmac_header = $_SERVER['HTTP_X_SHOPIFY_HMAC_SHA256'];
		$data = file_get_contents('php://input');
		
		$calculated_hmac = base64_encode(hash_hmac('sha256', $data, Yii::$app->params['shopify']['secret'], true));
		if ($debug){
			return true;
		} else {
			if ($hmac_header == $calculated_hmac){
				return true;
			} else{
				error_log('Webhook verified: '.var_export($verified, true)); //check error.log to see the result
				return false;
			}
		}
	}
	
	/**
	 * Function to take Jet Order Array and convert it to the Shopfiy array for uploading
	 */
	
	public function createShopifyOrderArray($jetArray, $note=null){
		//check if customer exists
		//*******************
		
		//Get a list of all products of the store
		$allProducts = $this->getAllProducts();
		
		//create the Shopify array
		$shopify = array (
			'order'=>array(
				//'id'=>$jetArray['merchant_order_id'],
				//'order_number'=>$details['reference_order_id'],
				//'tax_lines'=>array (),
				'source_name'=>"Jet",
				'note'=>"Order Created by JET",
				//'subtotal_price'=>'',
				'total_price' => $jetArray['order_totals']['item_price']['base_price'],
				//'total_tax'=>$details['order_totals']['item_price']['item_tax'],
				'customer'=>array(
						'first_name'=>substr($jetArray['buyer']['name'], 0, strpos($jetArray['buyer']['name'], " ") + 1),
						'last_name'=>substr($jetArray['buyer']['name'], strpos($jetArray['buyer']['name'], " ") + 1),
						'note'=>Yii::$app->params['jet']['verbiage']['created']
				),
				'shipping_address'=>array(
					'address1'=>$jetArray['shipping_to']['address']['address1'],
					'address2'=>$jetArray['shipping_to']['address']['address2'],
					'city'=>$jetArray['shipping_to']['address']['city'],
					//'company'=>'',
					//'country'=>'',
					'first_name'=>substr($jetArray['shipping_to']['recipient']['name'], 0, strpos($jetArray['shipping_to']['recipient']['name'], " ") + 1),
					'last_name'=>substr($jetArray['shipping_to']['recipient']['name'], strpos($jetArray['shipping_to']['recipient']['name'], " ") + 1),
					//'latitude'=>'',
					//'longitude'=>'',
					'phone'=>$jetArray['shipping_to']['recipient']['phone_number'],
					'province'=>$jetArray['shipping_to']['address']['state'],
					'zip'=>$jetArray['shipping_to']['address']['zip_code'],
					//'name'=>'',
					//'country_code'=>'',
					//'province_code'=>''
				),
				//'currency'=>'',
				'shipping_lines'=>array( array(
					'price'=>$jetArray['order_totals']['item_price']['item_shipping_cost'],
					'source'=>$jetArray['order_detail']['request_shipping_method'],
					'title'=>$jetArray['order_detail']['request_service_level'],
				)),
				'note_attributes'=>array(
					array(
						'name'=>'jet_reference_order_id',
						'value'=>$jetArray['reference_order_id']
					),
					array(
						'name'=>'jet_merchant_order_id',
						'value'=>$jetArray['merchant_order_id']
					)
				),
				'inventory_behaviour'=>'decrement_ignoring_policy'
			),
		);
		if ($note){
			$shopify['order']['note_attributes'][]=array(
				'name'=>'Old Shopify Order'	,
				'value'=>$note
			);
		}
		foreach ($jetArray['order_items'] as $ordDetail){
			//Check for Shopify product ID from product sku
			foreach ($allProducts['products'] as $key=>$variant){
				if ($variant['variants'][0]['sku']==$ordDetail['merchant_sku']){
					$varID = $variant['variants'][0]['product_id'];
					$varProdID = $variant['variants'][0]['id'];
					break;
				}
			}
				
			$shopify['order']['line_items'][]= array(
				'title' => $ordDetail['product_title'],
				'quantity'=>$ordDetail['request_order_quantity'],
				'price'=>$ordDetail['item_price']['base_price'],
				'product_id'=>$varID,
				'variant_id'=>$varProdID,
				'requires_shipping'=>true,
				'sku'=>$ordDetail['merchant_sku'],		
			);	
		}

		$shopify = json_encode($shopify);
		return $shopify;
	}
	
	/**
	 * 
	 * @return array of products
	 * Only needed for non-embeded apps
	 */
	public function verifyShopify($code, $hmac, $shop, $timestamp){
		
		//replace special characters
		//$code = str_replace("%", "%25",$code);
		//$code = str_replace("&", "%26",$code);
		//$code = str_replace("=", "%3D",$code);
		
		$message = "code=" . $code . "&shop=" . $shop . "&timestamp=" . $timestamp;
		$result = hash_hmac("sha256",$message , Yii::$app->params['shopify']['secret']);
		
		if ($result == $hmac){
			return "ok";	
		} else {
			return "$code";
		}
	}
	

}
