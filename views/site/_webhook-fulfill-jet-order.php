<?php 

/* @var $this yii\web\View */
use app\models\Jet;

/*==============================================================
 * Error handler to catch all the PHP errors, warnings,....
 ============================================================== */
function customErrorHandling($errno, $errstr) {
	throw new Exception("Error: [$errno] $errstr");
}

//Sets a user-defined error handler function
set_error_handler("customErrorHandling", E_ALL);

//Get header info
$request = getallheaders();
$headersJSON = json_encode($request);

//Verify webhook
try {
	$verify = new Jet();
	if ($verify->verify_webhook()){
		header("HTTP/1.1 200 OK");
		
		//Set Date/Time formate
		$iso8601 = "Y-m-d\TH:i:s.u0P" ;
		$date = new DateTime();
		$date->setTimezone(new DateTimeZone('America/New_York'));
		$response_shipment_date= $date->format($iso8601);
	
		//Get Shop
		$shop = $request['X-Shopify-Shop-Domain'];
		$shopifyOrderDetailsJson = file_get_contents('php://input');
		$shopifyOrderDetails = json_decode($shopifyOrderDetailsJson, true);
		$shopifyOrderId = $request['X-Shopify-Order-Id'];

		if ($shop && $shopifyOrderId){
			//Get Shopify Details
			$shopify = Jet::find()->where(['shop'=>$shop])->one();
			
			//Connect to Jet API
			$result = $shopify->connectJet(false, false, $shopify);

			//Find the Jet merchant order id based on note_attributes field
			foreach ($shopifyOrderDetails['note_attributes'] as $attribute){
				if($attribute['name']=="jet_merchant_order_id"){
					$jetMerchantOrderId = $attribute['value'];
					break;
				}
			}
			
			//Create Jet Data array to send for order fulfillment
			foreach ($shopifyOrderDetails['fulfillments'] as $fulfillments){
				if ($fulfillments['status']=='success'){
					foreach ($fulfillments['line_items'] as $item){
						$shipment_items[]=array(
							'merchant_sku'=>$item['sku'],
							'response_shipment_sku_quantity'=>$item['quantity'],
						);
					}
					$shipments[] =array(
						'alt_shipment_id'=>(string)$fulfillments['id'],
						'shipment_tracking_number'=>$fulfillments['tracking_number'],
						'response_shipment_date'=>$response_shipment_date,
						'carrier'=>$fulfillments['tracking_company'],
						'shipment_items'=>$shipment_items
					);
				}	
			}
			
			$data = array(
				'alt_order_id'=>(string)$shopifyOrderId,
				'shipments'=>$shipments
			);
			
			//Send shipment info to Jet
			$data = json_encode($data);
			$jetResult = $shopify->putOrderShipped($jetMerchantOrderId, $data);
			
			if ($jetResult[0] !="Success!"){
				Yii::info("Jet Error: " . json_encode($jetResult), "shopify.webhook.fulfill.jet.error." . $jetMerchantOrderId);
			} else {
				Yii::info("Order shipping info sent to Jet " . json_encode($jetResult), "shopify.webhook.fulfill.jet." . $jetMerchantOrderId);
				if ($shopify->Setup =="Ship order"){
					$setup = $shopify->progressSetup();
				}
			}
		} else {
			Yii::info("Error: No Shop and/or Order ID sent.", "shopify.webhook.fulfill.error");
		}
	} else {
		header("HTTP/1.1 403 Forbidden");
		Yii::error("Invalid Shopify Webhook - Verification failed {header Info: " . $headersJSON ."}", "shopify.webhook.fulfill.verifyError");
	}	
} catch (Exception $e) {
	header("HTTP/1.1 404 Error");
	Yii::error("Error : ".$e->getMessage() . " {header Info: " . $headersJSON ."}", "shopify.webhook.fulfill.error");
}
?>