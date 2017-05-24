<?php  
use app\models\jet_FulfillmentNodes;
use app\models\Jet;

$request = Yii::$app->request;

if (Yii::$app->request->post('node')) {
	$shopify_init = Jet::find()->where(['shop' => $request->get('shop')])->one();
	$jetModel = jet_FulfillmentNodes::find()->where(
			[
				'ShopifyStore'=>$request->get('shop'), 
				'FulfillmentNodeID'=>$request->post('node')
			])->exists();
			
	if (!$jetModel){
		$jetModel = new jet_FulfillmentNodes();
		
		$jetModel->FulfillmentName = $request->post('name');
		$jetModel->FulfillmentNodeID = $request->post('node');
		$jetModel->ShopifyStore = $request->get('shop');
		if(!$jetModel->save()){
			yii::error(json_encode($request), "jet.fulfillmentNode.add.error");
		} else {
			if ($shopify_init->Setup=="Add Fulfillment Node"){
				$setup = $shopify_init->progressSetup();
				echo $setup;
			}
		}
		
	} else {
		echo "Node Already Exists";
	}
} else {
	echo "Error: Please fill in all values";
}

?>