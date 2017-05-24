<?php

namespace app\models;

use Yii;
use linslin\yii2\curl;
use app\models\jet_SetupSteps;

/**
 *  
 */
class Jet extends Shopify
{
   	//Return values for forms
	public $alt_order_id;
    
    /**
     * @ return array the validation rules.
     */
    public function rules()
    {
        return [
        		[['shop', 'oauth_token'], 'required' ]
        ];
    }

	/**
	 * CURL PUT Execute
	 */
    public function executePutCurl($url, $data, $debug){
    	$session = Yii::$app->session;
    	//open connection
    	$curl = new curl\Curl();
    	$ch = curl_init();
    	
    	//set the url, number of POST vars, POST data
    	curl_setopt($ch,CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_ENCODING, "");
    	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    	curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    			'Content-Type: application/json',
    			'Authorization: bearer '.$session->get('id_token')
    	));
    	
    	//--------------Debug Info -------------------------------------
    	curl_setopt($ch, CURLOPT_VERBOSE, true);
    	$verbose = fopen('php://temp', 'rw+');
    	curl_setopt($ch, CURLOPT_STDERR, $verbose);
    	//-------------End Debug ---------------------------------------
    	
    	//execute post
    	$result = curl_exec($ch);
    	$result = json_decode($result, true);
    	
    	rewind($verbose);
    	$verboseLog = stream_get_contents($verbose);
    	
    	//close connection
    	curl_close($ch);
    	
    	//If there was an error in the curl, return the verbose
    	if ($result === FALSE) {
    		return false;
	    	Yii::error("cUrl error (#%d): %s<br>\n" . curl_errno($ch). htmlspecialchars(curl_error($ch)) . "<br><br>" . $verboseLog, "jet.executePutCurl.error");	    	
    	} else if (!$result){
	    	$result = array ("Success!");
	    } else if (array_key_exists('errors', $result)){
	    	if ($debug){
	    		$result['verboseLog']=$verboseLog;
	    	} else {
	    		$result = $result['errors'];
	    	}
	    } 
	   
	    return $result;
    }
	
	/**
	 * CURL POST Execute
	 */
    public function executePostCurl($url, $data, $debug){
    	$session = Yii::$app->session;
    	//open connection
    	$curl = new curl\Curl();
    	$ch = curl_init();
    	 
    	//set the url, number of POST vars, POST data
    	curl_setopt($ch,CURLOPT_URL, $url);
    	curl_setopt($ch,CURLOPT_POST, 1);
		curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($data));
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    			'Content-Type: application/json',
    			'Authorization: bearer '.$session->get('id_token')
    	));
    	 
    	//--------------Debug Info -------------------------------------
    	curl_setopt($ch, CURLOPT_VERBOSE, true);
    	$verbose = fopen('php://temp', 'rw+');
    	curl_setopt($ch, CURLOPT_STDERR, $verbose);
    	//-------------End Debug ---------------------------------------
    	 
    	//execute post
    	$result = curl_exec($ch);
    	$result = json_decode($result, true);
    	 
    	rewind($verbose);
    	$verboseLog = stream_get_contents($verbose);
    	 
    	//close connection
    	curl_close($ch);
    	 
    	//If there was an error in the curl, return the verbose
    	if ($result === FALSE) {
    		return false;
	    	Yii::error("cUrl error (#%d): %s<br>\n" . curl_errno($ch). htmlspecialchars(curl_error($ch)) . "<br><br>" . $verboseLog, "jet.executePostCurl.error");	    		 
	    } else if (!$result){
	    	$result = array ("Success!");
	    } else if (array_key_exists('errors', $result)){
	    	if ($debug){
	    		$result['verboseLog']=$verboseLog;
	    	} else {
	    		$result = $result['errors'];
	    	}
	    } 
	    return $result;
    }
	/**
	 * CURL GET Execute
	 */
    public function executeGetCurl($url, $debug){
    	$session = Yii::$app->session;
    	
    	//open connection
    	$curl = new curl\Curl();
    	$ch = curl_init();
    	
    	//set the url, number of POST vars, POST data
    	curl_setopt($ch,CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    			'Content-Type: application/json',
    			'Authorization: bearer '.$session->get('id_token')
    	));
    	
    	//--------------Debug Info -------------------------------------
    	curl_setopt($ch, CURLOPT_VERBOSE, true);
    	$verbose = fopen('php://temp', 'rw+');
    	curl_setopt($ch, CURLOPT_STDERR, $verbose);
    	//-------------End Debug ---------------------------------------
    	
    	//execute post
    	$result = curl_exec($ch);
    	//yii::trace($result, "jet.");
    	$result = json_decode($result, true);
    	    	
    	rewind($verbose);
    	$verboseLog = stream_get_contents($verbose);
    	
    	//close connection
    	curl_close($ch);
    	
    	//If there was an error in the curl, return the verbose
    	if ($result === FALSE) {
    		return false;
	    	Yii::error("cUrl error (#%d): %s<br>\n" . curl_errno($ch). htmlspecialchars(curl_error($ch)) . "<br><br>" . $verboseLog, "jet.executeGetCurl.error");	    	
    	} else if (!$result){
    		if ($debug){
    			$result=$verboseLog;
    		} else {
    			$result = array ("Success!");
    		}
	    } else if (array_key_exists('errors', $result)){
	    	if ($debug){
	    		$result['verboseLog']=$verboseLog;
	    	} else {
	    		$result = $result['errors'];
	    	}
	    } 
	    return $result;
    }
		
	/**
	 * https://developer.jet.com/docs/merchant-sku
	 * $sku is shopify defined SKU
	 * $data is JSON formatted data to upload
	 * 
	 */
	public function putSkuUpload($sku, $data, $debug=null){
		//set url
		$sku = str_replace(" ","%20", $sku);
		$url = Yii::$app->params['jet']['urls']['main'] . Yii::$app->params['jet']['urls']['merchant-skus'] . utf8_encode($sku);
		$result = $this->executePutCurl($url, $data, $debug);
		return $result;
	}
	
	/**
	 * https://developer.jet.com/docs/merchant-sku-price
	 * $sku is shopify defined SKU
	 * $data is JSON formatted data to upload
	 *
	 */
	public function putPriceUpload($sku, $data, $debug=null){
		$sku = str_replace(" ","%20", $sku);
		$url = Yii::$app->params['jet']['urls']['main'] . Yii::$app->params['jet']['urls']['merchant-skus'] . $sku . "/price";
		$result = $this->executePutCurl($url, $data, $debug);
		return $result;
		
	}
	
	/**
	 * https://developer.jet.com/docs/merchant-sku-inventory
	 * $sku is shopify defined SKU
	 * $data is JSON formatted data to upload
	 *
	 */
	public function putInventoryUpload($sku, $data, $debug=null){
		$sku = str_replace(" ","%20", $sku);
		$url = Yii::$app->params['jet']['urls']['main'] . Yii::$app->params['jet']['urls']['merchant-skus'] . $sku . "/Inventory";
		$result = $this->executePutCurl($url, $data, $debug);
		return $result;
	}
	
	/**
	 * https://developer.jet.com/docs/merchant-sku-price
	 * $sku is shopify defined SKU
	 * $data is JSON formatted data to upload
	 *
	 */
	public function getInventoryRetrieval($sku, $debug=null){
		$sku = str_replace(" ","%20", $sku);
		$url = Yii::$app->params['jet']['urls']['main'] . Yii::$app->params['jet']['urls']['merchant-skus'] . $sku ."/inventory";
		$result = $this->executeGetCurl($url, $debug);
		return $result;
	}
	
	/**
	 * https://developer.jet.com/docs/order-status
	 * 'created' - The order has just been placed. Jet.com allows a half hour for fraud check and customer cancellation. We ask that retailers NOT fulfill orders that are created.
	 * 'ready' - The order is ready to be fulfilled by the retailer
	 * 'acknowledged' - The order has been accepted by the retailer and is awaiting fulfillment
	 * 'inprogress' - The order is partially shipped
	 * 'complete' - The order is completely shipped or cancelled. All units have been accounted for
	 */
	public function getCheckOrder($status='ready', $debug=null){
		$url = Yii::$app->params['jet']['urls']['main'] .Yii::$app->params['jet']['urls']['orders'] . $status;
		$result = $this->executeGetCurl($url, $debug);
		return $result;
	}
	
	/**
	 * 
	 * @param string $status
	 * @param unknown $debug
	 */
	public function getCheckReturns($status='created', $debug=null){
		$url = Yii::$app->params['jet']['urls']['main'] .Yii::$app->params['jet']['urls']['returns'] . $status;
		$result = $this->executeGetCurl($url, $debug);
		return $result;
	}
	
	/**
	 *
	 * @param string $status
	 * @param unknown $debug
	 */
	public function getReturnDetails($id, $debug=null){
		$url = Yii::$app->params['jet']['urls']['main'] . $id;
		$result = $this->executeGetCurl($url, $debug);
		return $result;
	}
	
	/**
	 *https://developer.jet.com/docs/check-new-order-details
	 */
	public function getCheckOrderDetails($sku, $debug=null){
		$sku = str_replace(" ","%20", $sku);
		$url = Yii::$app->params['jet']['urls']['main'] . $sku;
		$result = $this->executeGetCurl($url, $debug);
		return $result;
	}
	
	/**
	 *https://developer.jet.com/docs/check-new-order-details
	 */
	public function putOrderAcknowledgement($id, $data, $debug=null){
		$url = Yii::$app->params['jet']['urls']['main'] . "/orders/" . $id . "/acknowledge";
		$result = $this->executePutCurl($url, $data, $debug);
		return $result; 
	}
	
	/**
	 * https://developer.jet.com/docs/ship-order
	 * @param unknown $id
	 * @param unknown $data
	 * @param unknown $debug
	 */
	public function putOrderShipped($id, $data, $debug=null){
		$url = Yii::$app->params['jet']['urls']['main'] . "/orders/" . $id . "/shipped";
		$result = $this->executePutCurl($url, $data, $debug);
		return $result;
	}
	
	public function putCompleteReturn($jet_return_id, $data, $debug=null){
		$url = Yii::$app->params['jet']['urls']['main'] . "/returns/" . $jet_return_id . "/complete";
		$result = $this->executePutCurl($url, $data, $debug);
		return $result;
	}
	
	/**
	 * 
	 */
	public function getJetTaxonomyNodes($offset=null, $limit=null,$debug=null){
		$url = Yii::$app->params['jet']['urls']['main'] . "/taxonomy/links/100?offset=" . $offset . "&limit=" . $limit;
		$result = $this->executeGetCurl($url, $debug);
		return $result;
	}
	
	public function updateTaxonomy($start=0, $offset=100){
		//Get all Taxonomy Nodes
		$node_urls = $this->getJetTaxonomyNodes();
		
		$i=0;
		foreach ($node_urls['node_urls'] as $key=>$node){
			if($i>=$start){
				$data[$i]=$node;
			}
			if($i>=($start+$offset)){
				break;
			}
			$i++;
		}

		foreach($data as $node){
			$nodeArray = $this->getJetTaxNode($node);
			if (($nodeArray[0] != "Success!") && ($nodeArray['jet_node_id'])!=NULL){
				$check = JetTaxonomy::find()->where(['jet_node_id'=>$nodeArray['jet_node_id']])->one();
				if(!$check){
					$add = new JetTaxonomy();
					$add->jet_node_id=$nodeArray['jet_node_id'];
					$add->jet_node_name =$nodeArray['jet_node_name'];
					$add->jet_node_path =$nodeArray['jet_node_path'];
					$add->amazon_node_ids =json_encode($nodeArray['amazon_node_ids']);
					$add->parent_id =$nodeArray['parent_id'];
					$add->jet_level =$nodeArray['jet_level'];
					$add->suggested_tax_code =$nodeArray['suggested_tax_code'];
						
					if ($add->save()){
						$message .= "Added " . $nodeArray['jet_node_id'] . "<br>";
					}
				} else {
					$message .= "Already exists :" . $nodeArray['jet_node_id'] . "<br>";
				}
			} else {
				$message .= "Node Error " . $node . "<br>";
			}
		}
		return $message;
	}
	
	/**
	 * 
	 * @param unknown $node
	 * @param unknown $debug
	 */
	public function getJetTaxNode($node, $debug=null){
		$url = Yii::$app->params['jet']['urls']['main'] . $node;
		$result = $this->executeGetCurl($url, $debug);
		return $result;
	}
	
	public function sendToJet($alt_order_id, $details, $status="accepted"){
		//Create PUT Body JSON for Jet acknowledgement
		$data = array (
			'acknowledgement_status'=>$status,
			'order_items'=>array(),
			'alt_order_id'=>(string)$alt_order_id
		);
		
		foreach ($details['order_items'] as $ordDetail){
			$data['order_items'][]=array(
				'order_item_acknowledgement_status'=>'fulfillable',
				'order_item_id'=>$ordDetail['order_item_id'],
			);
		}
		$data = json_encode($data, true);
		$jetResult = $this->putOrderAcknowledgement($details['merchant_order_id'], $data);
		
		return $jetResult;
	}
	/**
	 * Connect to JET
	 */
	public function connectJet($forceCheck=false, $debug=false, $shop=null){
		$session = Yii::$app->session;
			
		if (($session->get('id_token') && ($session->get('expires_on') > date('c'))) && ($forceCheck== False) ){
			return "You are connected to Jet.";
		} else {
			
			//Init curl
			$ch = new curl\Curl();
	
			//get http://example.com/
			$url = Yii::$app->params['jet']['urls']['main'] . Yii::$app->params['jet']['urls']['token'];
			
			if ($shop){
				//Get the Shop from the API Keys
				$shopify = Shopify::find()->where(['shop' => $shop->shop])->one();	
			} else {
				//get api keys from Shopify DB object
				$shopify = Shopify::find()->where(['shop' => $session->get('shop')])->one();
			}
			
			$fields = array(
				'user' => $shopify->jet_api_key,
				'pass' => $shopify->jet_pass
			);
			
			//JSON the data for the POST
			$fields=json_encode($fields);
			
			//open connection
			$ch = curl_init();
	
			//set the url, number of POST vars, POST data
			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_POST, 1);
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json'
			));
	
			//--------------Debug Info -------------------------------------
			curl_setopt($ch, CURLOPT_VERBOSE, true);
			$verbose = fopen('php://temp', 'rw+');
			curl_setopt($ch, CURLOPT_STDERR, $verbose);
			//-------------End Debug ---------------------------------------
	
			//execute post
			$result = curl_exec($ch);
			$result = json_decode($result, true);

			rewind($verbose);
			$verboseLog = stream_get_contents($verbose);
	
			//Error in CURL
			if ($result === FALSE) {
	    		Yii::error("cUrl error (#%d): %s<br>\n" . curl_errno($ch). htmlspecialchars(curl_error($ch)) . "<br><br>" . $verboseLog, "jet.connectJet.error");	    	
				
			//Error with credentials or connection
			} else if (array_key_exists('errors', $result)){
				if ($debug){
					$result['verboseLog']=$verboseLog;
				} else {
					$result = $result['errors'];
				}

				//Successful
			} else {
				$session->set('id_token', $result['id_token']);
				$session->set('expires_on', $result['expires_on']);
				$session->set('shop', $shopify->shop);
			
				if (!$forceCheck){
					$result = "You have successfully re-connected to Jet. Your current Token expires on " .$result['expires_on'] ;
				}
			}
			//close connection
			curl_close($ch);
			return $result;
		}
	}
	
	public function progressSetup(){
		$step = jet_SetupSteps::find()->where(['step'=>$this->Setup])->one();
		$nextStep = jet_SetupSteps::find()->where(['stepOrder'=>$step->stepOrder +1])->one();
		
		$this->Setup = $nextStep->step;
		if ($this->save()){
			return "Setup steps updated.";
		} else {
			return "error";
		}
	}
}
