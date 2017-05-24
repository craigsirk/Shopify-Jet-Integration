<?php 
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Shopify;
use app\models\Jet;
use app\models\JetFulfillmentNodes;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use kartik\editable\Editable;
use yii\helpers\Url;
use yii\widgets\Pjax;
use linslin\yii2\curl;

$session = Yii::$app->session;
$session->destroy();
$shop = 'craigsirk.myshopify.com';
$shopify = Jet::find()->where(['shop'=>$shop])->one();
$requestGet =  Yii::$app->request;
$jetCon = $shopify->connectJet(true, false, $shopify);

//Code to complete all JET orders
$updatedOrdersAck = $shopify->getCheckOrder('acknowledged');
$updatedOrdersProg = $shopify->getCheckOrder('inprogress');

$updatedOrders = array_merge($updatedOrdersAck['order_urls'], $updatedOrdersProg['order_urls']);



$iso8601 = "Y-m-d\TH:i:s.u0P" ;
$date = new DateTime();
$date->setTimezone(new DateTimeZone('America/New_York'));
$response_shipment_date= $date->format($iso8601);

if ($requestGet->get('action') == "ShipAll"){
	foreach ($updatedOrders as $key=>$id){
	
		//Get specific jet order details
		$jetOrderDetails = $shopify->getCheckOrderDetails($id);
	
		$data = array();
		$data['alt_order_id'] = $jetOrderDetails[alt_order_id];
		$data['shipments'][]=array(
				'shipment_tracking_number'=>$jetOrderDetails[alt_order_id] . "-0000",
				'response_shipment_date'=>$response_shipment_date,
				'carrier'=>$jetOrderDetails[order_detail][request_shipping_carrier],
		);
		foreach ($jetOrderDetails[order_items] as $value){
			$data[shipments][0][shipment_items][]=array(
					'merchant_sku'=>$value[merchant_sku],
					'response_shipment_sku_quantity'=>$value[request_order_quantity],
			);
		}
	
		$data = json_encode($data);
		$result = $shopify->putOrderShipped($jetOrderDetails[merchant_order_id], $data);
		echo "<pre>";
		print_r($result);
		echo "</pre>";
	}
} else if ($requestGet->get('action') == "cancel"){
	$id ='324b6329adb84744a5b798ba9eb43306';
	$alt = '5082159377';
	$sku1 = '123456789';
	$sku2 = 'Cool Product 1';
	
	$jetCancellationData = array(
		'alt_order_id'=> $alt,
		'shipments'=>array(
			array (
				'alt_shipment_id'=>"1234-3",
				'response_shipment_date'=>$response_shipment_date,
				//'carrier'=>"ups",
				'shipment_items'=>array(
					array(
						'merchant_sku'=>$sku1,
						'response_shipment_cancel_qty'=>1,
					)
				)
			)
		)
	);
	$jetCancellationData = json_encode($jetCancellationData);
	
	echo "<pre>";
	print_r($jetCancellationData);
	echo "</pre>";
	
	$jetCancellationResult = $shopify->putOrderShipped($id, $jetCancellationData);
	echo "<pre>";
	print_r($jetCancellationResult);
	echo "</pre>";
} else if ($requestGet->get('action') == "webhooks"){
	$webhooks = $shopify->getWebhook();

	
	$url = "https://" . $shopify->shop . Yii::$app->params['shopify']['admin'] . "/webhooks/520795665.json";
	//open connection
    	$ch = curl_init();
    	 
    	//set the url, number of POST vars, POST data
    	curl_setopt($ch,CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'X-Shopify-Access-Token: ' .$shopify->oauth_token,
			
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
    	echo "<pre>";
    	print_r($verboseLog);
    	echo "</pre>";
} else if ($requestGet->get('action') == "orders"){
	//$updatedOrders = $shopify->getCheckOrder('acknowledged');
	$updatedOrders = $shopify->getCheckOrder('inprogress');
	
	foreach ($updatedOrders[order_urls] as $id){
		//Get specific jet order details
		$jetOrderDetails = $shopify->getCheckOrderDetails($id);
	}
} else {
	//Setup Webhooks

	yii::info("hi", "jet.test");
	echo $session->get('shop');
	$result = $shopify->getWebhook();
	echo "<pre>";
	print_r($result);
	echo "</pre>";
}






