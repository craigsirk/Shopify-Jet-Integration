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

//Get all orders by ID
$returnOrders = $shopify_init->getCheckReturns();

//Pull the details of each order
foreach ($returnOrders['return_urls'] as $key=>$orders){
	$orderArray = $shopify_init->getReturnDetails($orders);
	
	$orderDetails[$key]['return_date'] = $orderArray['return_date'];
	$orderDetails[$key]['return_status'] = $orderArray['return_status'];
	$orderDetails[$key]['shipping_carrier'] = $orderArray['shipping_carrier'];
	$orderDetails[$key]['tracking_number'] = $orderArray['tracking_number'];
	$orderDetails[$key]['alt_order_id'] = $orderArray['alt_order_id'];
	$orderDetails[$key]['shop'] = $shopify_init->shop;
	$orderDetails[$key]['merchant_order_id'] = $orderArray['merchant_order_id'];
	$orderDetails[$key]['id']= $orders;
	$orderDetails[$key]['return_merchant_SKUs'] = json_encode($orderArray['return_merchant_SKUs']);
	$orderDetails[$key]['merchant_return_authorization_id'] = $orderArray['merchant_return_authorization_id'];
	
	
	foreach ($orderArray['return_merchant_SKUs'] as $key2=>$sku){
		$temp = $sku['requested_refund_amount']['principal'] + $sku['requested_refund_amount']['tax'] + $sku['requested_refund_amount']['shipping_cost'] + $sku['requested_refund_amount']['shipping_tax'];
		$orderDetails[$key]['total'] = $orderDetails[$key]['total'] + $temp;
	}
	//Get Shopify details
	$shopifyOrder = $shopify_init->getOrders("any", $orderArray['alt_order_id']);
	$orderDetails[$key]['order_number'] = $shopifyOrder['orders'][0]['order_number'];

}

$data = new ArrayDataProvider([
	'allModels' => $orderDetails,
	'id' => 'alt_order_id',
	'key'=> 'id',
	'pagination' => [
		'pageSize' => 10,
	],
	'sort' => [
		'attributes' => [
			'id',
			'return_date',
			'return_status',
			'tracking_number',
			'shipping_carrier',
			'order_number',
			'alt_order_id',
			'shop',
			'merchant_order_id',
			'return_merchant_SKUs',
			'total',
			'merchant_return_authorization_id'
		],
	],
]);
?>

<?= GridView::widget([
	'dataProvider' => $data,
	'id'=>'jet-orders-grid',
	'columns' => [
		[
			'class' => 'yii\grid\ActionColumn',
	        'header'=>'',
	        'template' => '{Complete}',
	        'buttons' => [
	            //view button
	            'Complete' => function ($url, $data) {
	                return Html::a('Complete', ['return-order', 'id'=>$data['merchant_return_authorization_id']], [
	           			'title' => Yii::t('app', 'Complete'),
	           			'class'=>'btn btn-primary btn-s' 
	                	
	                ]);
	            },
       	 	],
       	 	//'extraData'=>['shop'=>$session->get('shop')]
		],
		[
			'class'=>'kartik\grid\ExpandRowColumn',
			'width'=>'50px',
			'value'=>function ($data, $key, $index, $column) {
				return GridView::ROW_COLLAPSED;
			},
			'detailUrl'=>Url::toRoute(['expand-row-return-details']),
			'headerOptions'=>['class'=>'kartik-sheet-style'],
			'expandOneOnly'=>true,
			'enableRowClick'=>true,
			'expandIcon'=>"+",
			'collapseIcon'=>"-",
			'extraData'=>['shop'=>$session->get('shop')]
		],
		[
			'label'=>'Shopify Number',
			'attribute'=>'order_number',
			'value'=>function($data){
				return Html::a($data['order_number'], 'https://' . $data['shop'] . '/admin/orders/' . $data['alt_order_id'],  ['target'=>'_blank']);
			},
			'format' => 'raw'
		],
		[
			'attribute'=>'return_date',	
			'value'=>function($data){
				$utc_date = date("Y-m-d", strtotime($data['return_date']));
				return $utc_date;
			}
		],
		'return_status',
		'shipping_carrier',
		'tracking_number',
		[
			'label'=>'Total',
			'value' => function ($data) {
			return  "-$" . $data['total'];
				},
			'format' => 'raw'
		]
	]			    
]) ;

?>
