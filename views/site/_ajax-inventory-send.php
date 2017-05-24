<?php 

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\JetFulfillmentNodes;
use app\models\Shopify;
use app\models\Jet;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;

$request = Yii::$app->request;

//Get inventory update from JET
$shopify_init = Jet::find()->where(['shop' => $request->get('shop')])->one();

foreach ($request->post() as $key=>$post){
	$pos = strpos($key,"Quantity");
	if ($pos!== false){
		$temp = explode("-", $key);
		$data['fulfillment_nodes'][] = array (
			'fulfillment_node_id'=>$temp[2],
			'quantity'=>(int)$post	
		);
	}
}
$data = json_encode($data);

$results = $shopify_init->putInventoryUpload($request->post('sku'), $data);
if ($results[0] == "Success!"){
	$results = "Product Sku '" . $request->post('sku') . "' inventory uploaded successfully.";
	if ($shopify_init->Setup =="Send Inventory"){
		$setup = $shopify_init->progressSetup();
	}
} else {
	$results = "Product Sku '" . $request->post('sku') . "' inventory upload Error: " . json_encode($results);
	yii::error($results, "jet.inventory.error");
}

print_r($results);


