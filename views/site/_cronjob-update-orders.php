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
$output = array ();

//Set Shipping times
$iso8601 = "Y-m-d\TH:i:s.u0P" ;
$date = new DateTime();
$date->setTimezone(new DateTimeZone('America/New_York'));
$response_shipment_date= $date->format($iso8601);

foreach ($shops as $shop){
	try {
	//========================== Shop is set up in DB
		if ($shop->jet_api_key)	{
			//Get a list of all products of the store
			$allProducts = $shop->getAllProducts();
			
			//Connect to Jet API
			$result = $shop->connectJet(false, false, $shop);
			
//********************************************************************************************
// Check for updated orders to import into Shopify
//********************************************************************************************
			//Get all "acknowledged" order ids 
			$updatedOrdersAck = $shop->getCheckOrder('acknowledged');
			$updatedOrdersProg = $shop->getCheckOrder('inprogress');
			
			$updatedOrders = array_merge($updatedOrdersAck['order_urls'], $updatedOrdersProg['order_urls']);

			if ($updatedOrders){
				//foreach active jet order, check Jet details for each order 
				foreach ($updatedOrders as $id){
					//Get specific jet order details
					$jetOrderDetails = $shop->getCheckOrderDetails($id);

					//get Shopify order details
					$ShopifyOrderId = $jetOrderDetails['alt_order_id'];
					$ShopifyOrderArray = $shop->getOrders("any", $ShopifyOrderId);

					//***********
					//Now we can check each item in the jet order to see if there are any cancellations
					//**********
					foreach ($jetOrderDetails['order_items'] as $jetItems){
						
						$newShopifyID = null;
						//If there is a line item that has a cancellation, proceed to check
						if ($jetItems['request_order_cancel_qty']){
							//Now we need to check if the Shopify order items= jet order items, to see if it has already been captured
							foreach ($ShopifyOrderArray['orders'][0]['line_items'] as $shopifyItem){
								
								if ($jetItems['merchant_sku']==$shopifyItem['sku']){
									if ($shopifyItem['fulfillable_quantity'] > $jetItems['request_order_quantity']){
										//========We found an item that has been updated, cancel Shopify 
										$result = $shop->deleteOrder($ShopifyOrderId);
										
										//========Re-create Shopify with new values
										$note = Yii::$app->params['jet']['verbiage']['updated'] . "Old Shopify Order ID " . $ShopifyOrderArray['orders'][0]['order_number'] . " - " . $ShopifyOrderId;
										$shopify = $shop->createShopifyOrderArray($jetOrderDetails,$note); //Create the JSON to upload to Shopify
										$updatedResults = $shop->postOrder($shopify);
										$newShopifyID = $updatedResults['order']['id'];

										if($updatedResults){
											Yii::info("Successfully deleted order line items and updated Shopify ID " .$ShopifyOrderId . " to " . $newShopifyID, "shopify.order.delete." . $jetOrderDetails['merchant_order_id']);
																							
											//=======Send Cancelation fulfillment to Jet
											$cancelQty = $shopifyItem['quantity'] - $jetItems['request_order_quantity'];
											$jetCancellationData = array(
												'alt_order_id'=>(string)$newShopifyID,
												'shipments'=>array(
													array(
														'alt_shipment_id'=>$jetOrderDetails['alt_order_id'] . "-" . $shopifyItem['sku'],
														'response_shipment_date'=>$response_shipment_date,
														'carrier'=>$jetOrderDetails['order_detail']['request_shipping_carrier'],
														'shipment_items'=>array(
															array(
																'merchant_sku'=>$jetItems['merchant_sku'],
																'response_shipment_cancel_qty'=>$cancelQty,
															)
														)
													)
												)
											);
											
											$jetCancellationData = json_encode($jetCancellationData);
											
											//Send Cancellation to Jet
											$jetCancellationResult = $shop->putOrderShipped($jetOrderDetails['merchant_order_id'], $jetCancellationData);
											if ($jetCancellationResult){
												Yii::info("Jet Cancel Line Item Successful! " . json_encode($jetCancellationResult), "jet.order.update." . $jetOrderDetails['merchant_order_id']);
												if ($shop->Setup =="Cancel order"){
													$setup = $shop->progressSetup();
												}
											} else {
												Yii::info("Jet Cancel Line Item Error! " . $jetCancellationData, "jet.order.update." . $jetOrderDetails['merchant_order_id']);
											}
										} else {
											Yii::info("Shopify Update Line Items Error! " . $shopify, "shopify.order.update." . $jetOrderDetails['merchant_order_id']);	
										}
									}
									break;
								}
							}
						}
					}
					
					//************
					//Now we can update the Shopify (only can do the address and customer tho)
					//As long as $newShopifyID == null, which means we have not canclled the original shopify ordder
					//************
					if (!$newShopifyID){
						//check if there is an old order attribute
						$note = null;
						foreach ($ShopifyOrderArray['orders'][0]['note_attributes'] as $att){
							if ($att['name']=="Old Shopify Order"){
								$note = $att['value'];
								break;
							}
						}
						$shopifyJSON = $shop->createShopifyOrderArray($jetOrderDetails, $note); //Create Shopify array
						
						//update shopify order based on $ShopifyOrderId
						$result = $shop->putOrderUpdate($ShopifyOrderId, $shopifyJSON);
						if ($result){
							Yii::info("Successfully updated order Shopify ID " .$ShopifyOrderId, "shopify.order.update." . $jetOrderDetails['merchant_order_id']);
						} else {
							Yii::info("Error: Check Server Logs for updated order Shopify ID " .$ShopifyOrderId, "shopify.order.update." . $jetOrderDetails['merchant_order_id']);								
						}
					}
				}
			//========================= There are no new orders to process
			} else {
				//Yii::info("No updated orders.", "shopify.order.update.");
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




