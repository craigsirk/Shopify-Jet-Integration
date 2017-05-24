<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Jet;
use yii\data\ArrayDataProvider;
use kartik\grid\GridView;
use yii\helpers\Url;

$session = Yii::$app->session;
$this->title = 'Returns';
$request = Yii::$app->request;
try {
	$shopify_init = Jet::find()->where(['shop' => $request->post('shop')])->one();
	$orderArray = $shopify_init->getReturnDetails($request->post('expandRowKey'));

	foreach ($orderArray['return_merchant_SKUs'] as $key=>$sku){
		$skuDetails[$key]['alt_order_id'] = $orderArray['alt_order_id'];
		$skuDetails[$key]['merchant_sku'] = $sku['merchant_sku'];
		$skuDetails[$key]['reason'] = $sku['reason'];
		$skuDetails[$key]['return_quantity'] = $sku['return_quantity'];
		$skuDetails[$key]['requested_refund_amount_principal'] = $sku['requested_refund_amount']['principal'];
		$skuDetails[$key]['requested_refund_amount_tax'] = $sku['requested_refund_amount']['tax'];
		$skuDetails[$key]['requested_refund_amount_shipping_cost'] = $sku['requested_refund_amount']['shipping_cost'];
		$skuDetails[$key]['requested_refund_amount_shipping_tax'] = $sku['requested_refund_amount']['shipping_tax'];
	}
	$data = new ArrayDataProvider([
		'allModels' => $skuDetails,
		'id' => 'alt_order_id',
		'pagination' => [
			'pageSize' => 10,
		],
		'sort' => [
			'attributes' => [
				'merchant_sku',
				'reason',
				'return_quantity',
				'requested_refund_amount_principal',
				'requested_refund_amount_tax',
				'requested_refund_amount_shipping_cost',
				'requested_refund_amount_shipping_tax'
			],
		],
	]);
	?>
	<?= GridView::widget([
		'dataProvider' => $data,
		'id'=>'jet-return-grid',
		'columns' => [
			[
				'label'=>'Sku',
				'attribute'=>'merchant_sku', 
			],
			'reason',
			[
				'label'=>'Quantity',
				'attribute'=>'return_quantity',
			],
			[
				'label'=>'Principal',
				'attribute'=>'requested_refund_amount_principal',
				'value' => function ($data) {
					return  "$" . $data['requested_refund_amount_principal'];
				},
				'format' => 'raw'
			],
			[
				'label'=>'Tax',
				'attribute'=>'requested_refund_amount_tax',
				'value' => function ($data) {
					return  "$" . $data['requested_refund_amount_tax'];
				},
				'format' => 'raw'
			],
			[
				'label'=>'Shipping',
				'attribute'=>'requested_refund_amount_shipping_cost',
				'value' => function ($data) {
					return  "$" . $data['requested_refund_amount_shipping_cost'];
				},
				'format' => 'raw'
			],
			[
				'label'=>'Shipping Tax',
				'attribute'=>'requested_refund_amount_shipping_tax',
				'value' => function ($data) {
					return  "$" . $data['requested_refund_amount_shipping_tax'];
				},
				'format' => 'raw'
			],
		
		]			    
	]) ?>
<?php 
} catch (Exception $e){
	Yii::error("Error : ".$e->getMessage(), "shopify.return.error");
}




