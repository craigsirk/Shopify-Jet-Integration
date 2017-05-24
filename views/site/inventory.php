<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Products;
use yii\data\ArrayDataProvider;
use kartik\grid\GridView;
use yii\helpers\Url;

$session = Yii::$app->session;
$this->title = 'Inventory';

//get Post Request variables to update
$request = Yii::$app->request->post();

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
				],
		],
]);

//Check selected items and send to JET
if ($sendToJet = Yii::$app->request->post('selection')){
	foreach($sendToJet as $productID){
		//$product = $shopify_init->getProducts($productID);
		//$JSON = $product->formatProductUploadJSON();
		//$results = $shopify_init->sendToJet($productID, $JSON);
		echo "<pre>";
		//print_r($results);
		echo  "</pre>";
	}
}

//Create the Table view
?>
<div class="site-index">
    <div class="body-content">
        <div class="row">
        	<h2>Inventory</h2>
        	
        	<?php $form = ActiveForm::begin([
        		'id' => 'jet-add-form',
        		'options' => ['class' => 'form-horizontal'],
        		'method' => 'post',
        		'action' => ['/inventory'],
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
	        			'extraData'=>['shop'=>$session->get('shop')],
	        			'detailUrl'=>Url::toRoute(['expand-row-inventory-details']),
	        			'headerOptions'=>['class'=>'kartik-sheet-style'],
	        			'expandOneOnly'=>true,
	        			'enableRowClick'=>true,
	        			'expandIcon'=>"+",
	        			'collapseIcon'=>"-"
        			],
        			'product_title',
			    	'sku',
			    	[
			    		'attribute'=>'ShopifyPrice',
			    		'value' => function ($data) {
			                return  "$" . $data->ShopifyPrice;
			            },
			            'format' => 'raw'
			        ], 
			    	'inv_quantity'
					
			    ],
			    'responsive'=>true,
			    'hover'=>true
			    
			]) ?>
			<?=Html::submitButton('Update Selected Products Shopify Inv to Jet', ['class' => 'btn btn-primary',]);?>
			<?php ActiveForm::end(); ?>

        </div>
		
    </div>
</div>