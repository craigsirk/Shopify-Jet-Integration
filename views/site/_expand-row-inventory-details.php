<?php 

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\jet_FulfillmentNodes;
use app\models\Shopify;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;
use app\models\Jet;

$request = Yii::$app->request;

//Get inventory update from JET
$shopify_init = Jet::find()->where(['shop' => $request->post('shop')])->one();

//Get product SKU
$prod= $shopify_init->getProducts($request->post('expandRowKey'));
$sku= $prod[sku];

$invArray = $shopify_init->getInventoryRetrieval($sku);

//create a global for inventory total
$jetInvTotal = 0;

//Create Data Array for gridview
$data = jet_FulfillmentNodes::find()->where(['ShopifyStore'=>$request->post('shop')])->all();
foreach ($data as $node){
	$dataInvArray[$node->FulfillmentNodeID]['productID']=$request->post('expandRowKey');
	$dataInvArray[$node->FulfillmentNodeID]['sku']=$sku;
	$dataInvArray[$node->FulfillmentNodeID]['FulfillmentNodeID']=$node->FulfillmentNodeID;
	$dataInvArray[$node->FulfillmentNodeID]['FulfillmentName']=$node->FulfillmentName;
	$dataInvArray[$node->FulfillmentNodeID]['Quantity']=0;
	
	foreach ($invArray['fulfillment_nodes'] as $key=>$jetNode){
		if ($jetNode['fulfillment_node_id']==$node->FulfillmentNodeID){
			$dataInvArray[$node->FulfillmentNodeID]['Quantity']=$jetNode['quantity'];
			$jetInvTotal += $jetNode['quantity'];
			break;
		} 
	}
	
};

//$jetFulfillmentNode = new jet_FulfillmentNodes();
$dataProviderArray  = new ArrayDataProvider([
	'allModels' => $dataInvArray,
	'pagination' => [
		'pageSize' => 10,
	],
	'sort' => [
		'attributes' => [
			'sku',
			'productID',
			'FulfillmentNodeID',
			'FulfillmentName',
			'Quantity'
		],
	],
]);
//=================== Start Grid and Form ?>
<?= Html::beginForm([''], 'post', ['id' => $request->post('expandRowKey')]) ?>	
<?= Html::hiddenInput('sku', $sku);?>
	<div id="image-<?= $request->post('expandRowKey')?>" style="
		position:fixed; 
		top: 40%; 
		left:35%; 
		background: white;
		z-index: 999; 
		border:1px solid black;
		">
		<div style="padding:100px;">
			Please wait...<br>
			<img src="./assets/ajax-loader.gif" >
		</div>
	</div>
<?= GridView::widget([
		'dataProvider' => $dataProviderArray,
		'id'=>'jet-upload-ful-grid',
		'pjax'=>true,
		'toolbar'=>false,
		'panel'=>[
			'heading'=>false,
		],
		'columns' => [
			'FulfillmentName',
			'FulfillmentNodeID',
			[
				'attribute'=>'Quantity',
				'label'=>'Jet Inventory',
				'value' => function($dataProviderArray){
			        return Html::textInput('Quantity-' .$dataProviderArray['productID'] . '-' .$dataProviderArray['FulfillmentNodeID'], $dataProviderArray['Quantity']);
			    },
			    'format' => 'raw',
			    'pageSummary'=>'<span id="total-' . $request->post('expandRowKey') .'">Total ' . $jetInvTotal . '</span>',
			]
		],
		'showPageSummary' => true
]);
?>
<?= html::button('Update Inventory', ['class' => 'btn btn-primary', 'id'=>'update-'. $request->post('expandRowKey')]);?>
<?= Html::endForm() ?>

<script>
$(document).ready(function(){
	
	//when the user clicks off of the quantity field:
	//This needs work to update the total row on click of input text
	$('#enduser-zip').keyup(function(){

	});

	//Ajax for updating inventory to jet
	$('#image-<?= $request->post('expandRowKey')?>').hide();
	$('#update-<?= $request->post('expandRowKey') ?>').click(function(e){
		$.ajax({
		    type: 'POST',
		    url: '/ajax-inventory-send?shop=<?= $request->post('shop') ?>', 
		    data: $("#<?= $request->post('expandRowKey')?>").serialize(),
		    success: function(msg){
		        alert(msg);
		        window.location = window.location.pathname;
		        $('#image-<?= $request->post('expandRowKey')?>').hide();
		    },
		    beforeSend : function (){
		    	$('#image-<?= $request->post('expandRowKey')?>').show();
            },
            complete: function(){
                $('#image-<?= $request->post('expandRowKey')?>').hide();
            },
		})
	})
});


</script>