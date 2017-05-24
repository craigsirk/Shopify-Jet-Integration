<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Products;
use yii\data\ArrayDataProvider;
use kartik\grid\GridView;
use yii\helpers\Url;

$session = Yii::$app->session;
$this->title = 'Products';

//get Post Request variables to update
$request = Yii::$app->request->post();

//Check selected items and send to JET
if ($sendToJet = Yii::$app->request->post('selection')){
	foreach($sendToJet as $productID){
		$product = $shopify_init->getProducts($productID);
		$JSON = $product->formatProductUploadJSON();
		$results = $shopify_init->putSkuUpload($product[sku], $JSON);
		
		//updated meta data for uploaded
		$uploaded = $shopify_init->postProductMetadata($product[id], "jet_settings", "uploaded", "Yes", "string", "Has the product been uploaded to Jet?");
		
		if ($results[0] == "Success!"){
			$results = "Product Sku '" . $product[sku] . "' uploaded successfully.";
			if ($shopify_init->Setup =="Send Merchant SKU"){
				$setup = $shopify_init->progressSetup();
			}
		} else {
			$results = "Product Sku '" . $product[sku] . "' upload Error: " . json_encode($results);
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
			'vendor',
			'product_type',
			'ShopifyPrice',
			'inv_quantity',
			'jet_price',
			'uploaded'
		],
	],
]);
?>
<div class="site-index">
    <div class="body-content">
        <div class="row">
        	<h2>Products</h2>
        	
        	<?php $form = ActiveForm::begin([
        		'id' => 'jet-add-form',
        		'options' => ['class' => 'form-horizontal'],
        		'method' => 'post',
        		'action' => ['/products'],
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
					    'class'=>'kartik\grid\ExpandRowColumn',
					    'width'=>'50px',
					    'value'=>function ($data, $key, $index, $column) {
					        return GridView::ROW_COLLAPSED;
					    },
					    'detailUrl'=>Url::toRoute(['expand-row-product-details', 'shop'=>$session->get('shop')]),
					    'headerOptions'=>['class'=>'kartik-sheet-style'], 
					    'expandOneOnly'=>true,
					    'enableRowClick'=>true,
					    'expandIcon'=>"+",
					    'collapseIcon'=>"-"
					],
        			'product_title',
			    	'sku',
			    	'vendor',
			    	'product_type',
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
				        	return  "$" . $data->jet_price;
				        },
				        'format' => 'raw'
			        ],
			    	'inv_quantity',
			    	[
				    	'attribute'=>'uploaded',
				    	'value' => function ($data) {
				    		return $data->uploaded . Html::hiddenInput($data->id . ".uploaded", $data->uploaded);
				    	},
				    	'format' => 'raw'
			    	],
					
			    ],
			    'responsive'=>true,
			    'hover'=>true
			    
			]) ?>
			<?=Html::submitButton('Send Selected', ['class' => 'btn btn-primary',]);?>
			<?php ActiveForm::end(); ?>

        </div>
		
    </div>
</div>


