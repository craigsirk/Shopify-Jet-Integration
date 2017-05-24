<?php

/* @var $this yii\web\View */
use app\models\Jet;
use linslin\yii2\curl;

/*==============================================================
 * Error handler to catch all the PHP errors, warnings,....
 ============================================================== */
function customErrorHandling($errno, $errstr) {
	throw new Exception("Error: [$errno] $errstr");
}

//Sets a user-defined error handler function
set_error_handler("customErrorHandling", E_ALL);

$requestGet = Yii::$app->request;

if ($requestGet->get('override')==true){
	$shops = Jet::find()->where(['shop'=>$requestGet->get('shop')])->all();
} else {
	$shops = Jet::find()->where(['Setup'=>'Complete'])->all();
}
$session = Yii::$app->session;
$output = array ('time'=>date('c'));

foreach ($shops as $key=>$shop){
	try {
	//========================== Shop is set up in DB
		if ($shop->jet_api_key)	{
				
			//Connect to Jet API
			$result = $shop->connectJet(false, false, $shop);

//********************************************************************************************
// Check for ready orders to import into Shopify 
//********************************************************************************************
			$readyOrders = $shop->getCheckOrder();

			if ($readyOrders['order_urls']){
				//Acknowledge orders
				foreach ($readyOrders['order_urls'] as $key=>$id){
					//get specific order details from JET
					$details = $shop->getCheckOrderDetails($id);
					if ($shop->Setup=="Cancel order"){
						foreach ($details['order_items'] as $key=>$ordDetail){
							$details['order_items'][$key]['request_order_quantity']=$ordDetail['request_order_quantity'] + $ordDetail['request_order_cancel_qty'];
						}

					}
					//***************************
					//import to Shopify
					//***************************
					$shopify = $shop->createShopifyOrderArray($details); //Create the JSON to upload to Shopify
					$ShopifyResult = $shop->postOrder($shopify);

					if ($ShopifyResult){
						Yii::info("Order added to Shopify. " . json_encode($ShopifyResult), "shopify.order.acknowledge." . $details['merchant_order_id']);
						
						//***************************
						//Send to Jet
						//***************************
						$jetResult = $shop->sendToJet($ShopifyResult['order']['id'], $details);
					
						if ($jetResult){
							Yii::info("Jet Acknowledgement Successful! " . json_encode($jetResult), "jet.order.acknowledge." . $details['merchant_order_id']);
							if ($shop->Setup =="Acknowledge order"){
								$setup = $shop->progressSetup();
							}
						} else {
							Yii::info("Jet Error: Check Server Logs", "jet.order.acknowledge." . $details['merchant_order_id']);
						}
					} else {
						Yii::info("Shopify Error: Check Server Logs", "shopify.order.acknowledge." . $details['merchant_order_id']);
					}
				}	
			} else {
				//Yii::info("No new orders.", "jet.order.acknowledge");
			}
//========================== Shop is not set up in DB
		} else {
			//Yii::info("Shop not configured.", "shopify.order.error");
		}
//========================== Some sort of error
	} catch (Exception $e) {
		Yii::error("Error : ".$e->getMessage(), "shopify.order.error");
	}
}




