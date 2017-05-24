<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use app\models\Jet;
use yii\data\ArrayDataProvider;
use kartik\grid\GridView;


$session = Yii::$app->session;
$this->title = 'Orders';

//Get all orders by ID
$readyOrdersAck = $shopify_init->getCheckOrder('acknowledged');
$readyOrdersProg = $shopify_init->getCheckOrder('inprogress');
			
$readyOrders = array_merge($readyOrdersAck['order_urls'],$readyOrdersProg['order_urls']);
			

//Pull the details of each order
foreach ($readyOrders as $key=>$orders){
	$orderArray =$shopify_init->getCheckOrderDetails($orders);
	$orderDetails[$key]['order_placed_date'] = $orderArray['order_placed_date'];
	$orderDetails[$key]['status'] = $orderArray['status'];
	//$orderDetails[$key][order] = $orderArray['order'];
	//$orderDetails[$key][qantity] = $orderArray['qantity'];
	//$orderDetails[$key][sku] = $orderArray['sku'];
	$orderDetails[$key]['request_shipping_method'] = $orderArray['order_detail']['request_shipping_method'];
	$orderDetails[$key]['request_service_level'] = $orderArray['order_detail']['request_service_level'];
	$orderDetails[$key]['request_ship_by'] = $orderArray['order_detail']['request_ship_by'];
	$orderDetails[$key]['request_delivery_by'] = $orderArray['order_detail']['request_delivery_by'];
}

$data = new ArrayDataProvider([
	'allModels' => $orderDetails,
	'id' => 'alt_order_id',
	'pagination' => [
		'pageSize' => 10,
	],
	'sort' => [
		'attributes' => [
			'order_placed_date',
			'status',
			'order',
			'qantity',
			'sku',
			'request_shipping_method',
			'request_service_level',
			'request_ship_by',
			'request_delivery_by'
		],
	],
]);
?>
<?php //*****************  Loading Image *****************?>
	<div id="image" style="display:none;position:fixed; top: 40%; left:35%; background: white;z-index: 1000; border:1px solid black;">
		<div style="padding:100px;">
			Please wait...<br>
			<img src="./assets/ajax-loader.gif" >
		</div>
	</div>
	<?php //***************** end loading image **************?>
<div class="row">
	<div class="col-lg-3">
		<?= Html::Button('Check for new/updated Orders', ['class' => 'btn btn-primary', 'id'=>'check']);?>
	</div>
</div>
<br><br>
<?= GridView::widget([
	'dataProvider' => $data,
	'id'=>'jet-orders-grid',
	'columns' => [
		[
		'class' => 'yii\grid\CheckboxColumn', 'checkboxOptions' => function($data) {
			return ['value' => $data->id];
			},
		],
		[
			'attribute'=>'order_placed_date',	
			'value'=>function($data){
				$utc_date = date("Y-m-d h:i a", strtotime($data->order_placed_date));
				$local_date = $utc_date;
				return $local_date;
			}
		],
		'status',
		'order',
		'qantity',
		'sku',
		'request_shipping_method',
		'request_service_level',
		'request_ship_by',
		'request_delivery_by'
	]			    
]) ?>

<script>
$('#image').hide();
$('#check').click(function(e){
	$.ajax({
	    type: 'POST',
	    url: '/cronjob-acknowledge-orders?shop=<?= $shopify_init->shop ?>&override=true', 
	    success: function(msg){
		    
		    $.ajax({
			    type:'POST',
			    url: '/cronjob-update-orders?shop=<?= $shopify_init->shop ?>&override=true',
			    success: function(msg){
			    
			    	location.reload();
			    	$('#image').hide();
			    }
		    }); 
	    },	
	    beforeSend : function (){
	    	$('#image').show();
        },
        complete: function(){
            $('#image').hide();
        },    
	})
});
</script>
