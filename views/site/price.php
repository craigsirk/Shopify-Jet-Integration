<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Products;
use yii\data\ArrayDataProvider;
use kartik\grid\GridView;
use yii\helpers\Url;

$session = Yii::$app->session;
$this->title = 'Price';

//get Post Request variables to update
$request = Yii::$app->request->post();

//Set a blank product to get attributes and hints
$prod = new Products();

//Check selected items and send to JET
if ($sendToJet = Yii::$app->request->post('selection')){
	foreach($sendToJet as $productID){
		//Update Shopify Metadata
		$meta = $shopify_init->postProductMetadata($productID, 'jet_settings', 'jet_price', Yii::$app->request->post($productID."_jet_price"), $prod->types['jet_price'], substr($prod->getAttributeHint('jet_price'),0,254));
		
		//Set data to be sent on price
		$data = '{"price":' . str_replace (",", "", Yii::$app->request->post($productID."_jet_price")) . '}';
		
		//send to Jet
		$results = $shopify_init->putPriceUpload(Yii::$app->request->post($productID."_sku"), $data);
		if ($results[0] == "Success!"){
			$results = "Product Sku '" . Yii::$app->request->post($productID."_product_title"). "' uploaded successfully.";
			if ($shopify_init->Setup =="Send Price"){
				$setup = $shopify_init->progressSetup();
			}
		} else {
			$results = "Product Sku '" . Yii::$app->request->post($productID."_product_title") . "' upload Error: " . json_encode($results);
		}
		
		echo "<pre>";
		print_r($results);
		echo  "</pre>";
	}
}


//Create the Data Model for the grid view
$products = $shopify_init->getProducts();

$data = new ArrayDataProvider([
		'allModels' => $products,
		'key'=>'id',
		'pagination' => [
				'pageSize' => 10,
		],
		'sort' => [
				'attributes' => [
						'id',
						'product_title',
						'sku',
						'ShopifyPrice',
						'map_price',
						'jet_price'
				],
		],
]);

//Create the Table view
?>
<div class="site-index">
    <div class="body-content">
        <div class="row">
        	<h2>Price</h2>
        	
        	<?php $form = ActiveForm::begin([
        		'id' => 'jet-add-form',
        		'options' => ['class' => 'form-horizontal'],
        		'method' => 'post',
        		'action' => ['/price'],
        		'fieldConfig' => [
        				'template' => "{label}\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-11\">{error}</div>",
        				'labelOptions' => ['class' => 'col-lg-3 control-label'],
        		],
	        ]);
	        ?>  
	             	        	
        	<?= GridView::widget([
			    'dataProvider' => $data,
        		'id'=>'jet-add-grid',
			    'columns' => [
			    	[
			    		'class' => 'yii\grid\CheckboxColumn', 'checkboxOptions' => function($data) {
			                return ['value' => $data->id];
			            },
        			],
        			[
        				'attribute'=>'product_title',
	        			'value' => function ($data) {
	        				return $data->product_title . Html::hiddenInput($data->id . ".product_title", $data->product_title);
	        			},
	        			'format' => 'raw'
        			],
        			[
	        			'attribute'=>'sku',
	        			'value' => function ($data) {
	        				return $data->sku . Html::hiddenInput($data->id . ".sku", $data->sku);
	        			},
	        			'format' => 'raw'
        			],
			    	[
			    		'attribute'=>'ShopifyPrice',
			    		'value' => function ($data) {
			                return  "$" . $data->ShopifyPrice;
			            },
			            'format' => 'raw'
			        ], 
			        [
				        'attribute'=>'jet_price',
				        'value' => function ($data) {
				        	return "$" . Html::textInput($data->id . ".jet_price", $data->jet_price);
				        },
				        'format' => 'raw'
			        ],
			        [
				        'attribute'=>'map_price',
				        'value' => function ($data) {
				        	return  "$" . $data->map_price;
				        },
				        'format' => 'raw'
			        ],
			    	'inv_quantity'
					
			    ],
			    'responsive'=>true,
			    'hover'=>true
			    
			]) ?>
			<?=Html::submitButton('Send Selected', ['class' => 'btn btn-primary',]);?>
			<?php ActiveForm::end(); ?>

        </div>
		
    </div>
</div>