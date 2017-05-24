<?php 
/* @var $this yii\web\View */
use app\models\Jet;

$session = Yii::$app->session;
$request = Yii::$app->request;

try {
	//Get Shopify Details
	$shopify = Jet::find()->where(['shop'=>$session->get('shop')])->one();
	//Get Shopify order
	$order = $shopify->getOrders("any",$request->post('alt_order_id'), "line_items");
	//Default note for shopify
	$note = "";
	
	//Connect to Jet API
	$jetConnection = $shopify->connectJet(false, false, $shopify);
	
	//ReturnArray is the JSON for JET
	$returnArray = array(
		'merchant_order_id'=>$request->post('merchant_order_id'),
		'alt_order_id'=>$request->post('alt_order_id'),
		'agree_to_return_charge'=>true
	);
	$shipping = 0;
	foreach ($request->post('items') as $key=>$item){
		$returnArray['items'][$key]=array(
			'order_item_id'=>$item['order_item_id'],
			'total_quantity_returned'=>(int)$item['return_quantity'],
			'order_return_refund_qty'=>(int)$item['recieved_quantity'],
			'refund_amount'=>array(
				'principal'=>(float)str_replace("$","", $item['Approved']['principal']),
				'tax'=>(float)str_replace("$","", $item['Approved']['tax']),
				'shipping_cost'=>(float)str_replace("$","", $item['Approved']['shipping_cost']),
				'shipping_tax'=>(float)str_replace("$","", $item['Approved']['shipping_tax']),
			)
		);
		
		//Find the Shopify order line item id
		foreach ($order['orders'][0]['line_items'] as $shopifyItems){
			if ($shopifyItems['sku']==$item['merchant_sku']){
				$refundItems[$key]=array(
					'line_item_id'=>$shopifyItems['id'],
					'quantity'=>(int)$item['recieved_quantity']
				);
				$note .= "x" . $item['recieved_quantity'] . ": " .$shopifyItems['sku'] . " Refunded by Jet\n";
				break;
			}
		}
		
		$shipping += (float)str_replace("$","", $item['Approved']['shipping_cost']) + (float)str_replace("$","", $item['Approved']['shipping_tax']);
		if ($item['Approved']['feedback']){
			$returnArray['items'][$key]['return_refund_feedback']=$item['Approved']['feedback'];
			$returnArray['items'][$key]['notes']=$item['Approved']['notes'];
		}
	}
	
	//Add return info to shopify
	$refundData = array(
		'refund'=>array(
			'restock'=>true,
			'note'=>$note,
			'shipping'=>array(
				'full_refund'=>'false',
				'amount'=>$shipping
			),
			'refund_line_items'=>$refundItems
		)
	);
				
	//check if it is rejected
	if ($request->get('action')=="reject"){
		$returnArray['agree_to_return_charge']=false;
		foreach ($returnArray['items'] as $key=>$item){
			$returnArray['items'][$key]['refund_amount']['principal'] = 0;
			$returnArray['items'][$key]['refund_amount']['tax'] = 0;
			$returnArray['items'][$key]['refund_amount']['shipping_cost'] = 0;
			$returnArray['items'][$key]['refund_amount']['shipping_tax'] = 0;
		}
		$returnArray['return_charge_feedback']=$request->post('list');	
	} 
	
	//Send to Jet
	$returnArray = json_encode($returnArray);
	$result = $shopify->putCompleteReturn($request->post('merchant_return_authorization_id'), $returnArray);

	if ($result[0] !="Success!"){ 
		echo "Jet Error";
		Yii::info("Jet Error: " . json_encode($result), "jet.ajax.return.error." . $request->post('merchant_return_authorization_id'));
	} else {
		Yii::info("Return acknowledge with Jet " . json_encode($result), "jet.ajax.return." . $request->post('merchant_return_authorization_id'));
		
		//Add refund to Shopify
		$refund = $shopify->postOrderRefund($request->post('alt_order_id'), json_encode($refundData), "json");
		if (!$refund){
			echo "Shopify Error";
			Yii::info("Refund Error Shopify " . json_encode($refund), "shopify.ajax.return.error" . $request->post('merchant_return_authorization_id'));
		} else {
			Yii::info("Refund Shopify " . json_encode($refund), "shopify.ajax.return." . $request->post('merchant_return_authorization_id'));	
				
			if ($shopify->Setup =="Complete return"){
				$setup = $shopify->progressSetup();
			}
			echo "Success!";
		}
	}
	
} catch (Exception $e) {
	Yii::error("Error : ".$e->getMessage(), "jet.return.error");
}

?>
