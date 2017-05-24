<?php  
use yii\helpers\Json;
use app\models\jet_FulfillmentNodes;

$out = Json::encode(['output'=>'test', 'message'=>'']);

// validate if there is a editable input saved via AJAX
if (Yii::$app->request->post('hasEditable')) {

	// instantiate your  model for saving, $nodeId is json
	$nodeId = Yii::$app->request->post('editableKey');
	$nodeId = json_decode($nodeId, true);//Turn it into an array
	$jetModel = jet_FulfillmentNodes::find()->where(['ShopifyStore'=>$nodeId[ShopifyStore], 'FulfillmentNodeID'=>$nodeId[FulfillmentNodeID]])->one();

	$posted = current($_POST['jet_FulfillmentNodes']);
	$post = ['jet_FulfillmentNodes' => $posted];
	
	//Get the updated node id
	if ($post[jet_FulfillmentNodes][FulfillmentNodeID]){
		$newNodeId = $post[jet_FulfillmentNodes][FulfillmentNodeID];//get the updated value
		$jetModel->FulfillmentNodeID = $newNodeId;
	} else if ($post[jet_FulfillmentNodes][FulfillmentName]){
		$newName = $post[jet_FulfillmentNodes][FulfillmentName];
		$jetModel->FulfillmentName = $newName;
	}
	
	if ($jetModel->save()) {
		$output = $newNodeId;
		$message = '';
	} else {
		yii::error(json_encode(Yii::$app->request), "jet.fulfillmentNode.update.error");
		$message = 'error';
	}

	$out = Json::encode(['output'=>$output, 'message'=>$message]);
}
echo $out;
