<?php  
use app\models\jet_FulfillmentNodes;

$request = Yii::$app->request;

if (Yii::$app->request->post('node')) {
	$jetModel = jet_FulfillmentNodes::find()->where(['ShopifyStore'=>$request->get('shop'), 'FulfillmentNodeID'=>$request->post('node')])->one();
	if(!$jetModel->delete()){
		yii::error(json_encode($request), "jet.fulfillmentNode.delete.error");
	}
} else {
	echo "Error";
	yii::error(json_encode($request), "jet.fulfillmentNode.delete.error");
}

?>