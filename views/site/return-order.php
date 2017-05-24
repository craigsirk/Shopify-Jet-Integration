<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use app\models\Jet;
use yii\helpers\Url;

$session = Yii::$app->session;
$this->title = 'Return Order';
$request = Yii::$app->request;
setlocale(LC_MONETARY, 'en_US');

try {
	$shopify_init = Jet::find()->where(['shop' => $session->get('shop')])->one();
	$url="/returns/state/" . $request->get('id');
	$orderArray = $shopify_init->getReturnDetails($url, "true");
	
	$reasonArray = array(
		''=>'',
		'other' => 'Other',
		'item damaged' =>'Item damaged',
		'not shipped in original packaging'=> 'Not shipped in original packaging',
		'customer opened item'=>'Customer opened item',
		'general adjustment - notes required'=>'General adjustment - notes required',
	);
	$rejectArray = array(
			''=>'',
			'other' => 'Other',
			'outsideMerchantPolicy' =>'Outside Merchant Policy',
			'notMerchantError'=> 'Not Merchant Error',
			'wrongItem'=>'Wrong Item',
			'fraud'=>'Fraud',
			'returnedOutsideWindow'=>'Return Outside Window'
	);
	?>
	<?php //****************** Start Form *********************************?>
	<?= Html::beginForm(['return-order', 'action' => 'complete'], 'post', [
			'class' => 'form-horizontal',
			'id'=> 'return-form' 
		]); 
	?>
	<?= Html::hiddenInput('merchant_order_id', $orderArray['merchant_order_id']); ?>
	<?= Html::hiddenInput('merchant_return_authorization_id', $orderArray['merchant_return_authorization_id']); ?>

	<?php //*****************  Loading Image *****************?>
	<div id="image" style="display:none;position:fixed; top: 40%; left:35%; background: white;z-index: 1000; border:1px solid black;">
		<div style="padding:100px;">
			Please wait...<br>
			<img src="./assets/ajax-loader.gif" >
		</div>
	</div>
	<?php //***************** end loading image **************?>
	<?php //***************** Reject text area ***************?>
	<div id="reject-ajax" style="display:none; position:fixed; top: 30%; left:25%; background: white;z-index: 999; border:1px solid black;">
		<div style="padding:100px;">
			<div>
				<?= Html::label('Please select a reason for rejection:', 'reason-rejection', ['class' => 'control-label']) ?>
				<br>
				<?= Html::dropDownList('list', '', $rejectArray) ?>		
			</div>
			<div style="padding:20px;">
				<?= Html::Button('Reject Return', ['class' => 'btn btn-primary', 'id'=>'reject-return']);?>
				<?= Html::Button('Cancel', ['class' => 'btn btn-primary', 'id'=>'cancel']);?>
			</div>
		</div>
	</div>
	<?php //****************** End reject text area **********************?>
	<div class='row'>
		<div class='col-sm-3'>
			<div class='form-group'>
				<?= Html::label('Id', 'alt_order_id', ['class' => 'control-label']) ?>
				<?= Html::input('text', 'alt_order_id', $orderArray['alt_order_id'], ['readonly' => true, 'class'=>'form-control']);?>
			</div>
		</div>
		<div class='col-sm-1'></div>
		<div class='col-sm-3'>
			<div class='form-group'>
				<?= Html::label('Return Date', 'return_date', ['class' => 'control-label']) ?>
				<?= Html::input('text', 'return_date', date("Y-m-d", strtotime($orderArray['return_date'])), ['readonly' => true, 'class'=>'form-control']);?>
			</div>
		</div>
	</div>
	<?php 
	foreach ($orderArray['return_merchant_SKUs'] as $key=>$sku){
		?>
		<?= Html::hiddenInput("items[$key][order_item_id]", $sku['order_item_id']); ?>
		
		<hr>
		<div class='row'>
			<div class='col-sm-3'>
				<div class='form-group'>
					<?= Html::label('Product Sku #' . ($key +1), 'merchant_sku', ['class' => 'control-label']) ?>
					<?= Html::input('text', "items[$key][merchant_sku]", $sku['merchant_sku'], ['readonly' => true, 'class'=>'form-control']);?>
				</div>
			</div>
		
		</div>
		<div class='row'>
			<div class='col-sm-3'>
				<div class='form-group'>
					<?= Html::label('Quantity Returned', 'return_quantity', ['class' => 'control-label']) ?>
					<?= Html::input('text', "items[$key][return_quantity]", $sku['return_quantity'], ['readonly' => true, 'class'=>'form-control']);?>
				</div>
			</div>
			<div class='col-sm-1'></div>
			<div class='col-sm-3'>
				<div class='form-group'>
					<?= Html::label('Quantity Recieved', 'recieved_quantity', ['class' => 'control-label']) ?>
					<?= Html::input('text', "items[$key][recieved_quantity]", $sku['return_quantity'], ['class'=>'form-control']);?>
				</div>
			</div>
		</div>
		<div class='row'>
			<div class='col-sm-7'>
				<div class='form-group'>
					<?= Html::label('Return Reason', 'reason', ['class' => 'control-label']) ?>
					<?= Html::input('text', "items[$key][reason]", $sku['reason'], ['readonly' => true, 'class'=>'form-control']);?>
				</div>
			</div>

		</div>
		<div class='row'>
			<div class='col-sm-3'>
				<div class='form-group'>
					<?= Html::label('Principal Requested', 'principal-label', ['class' => 'control-label']) ?>
					<?= Html::input('text', "items[$key][Requested][principal]", money_format('%n',$sku['requested_refund_amount']['principal']), ['readonly' => true, 'class'=>'form-control']);?>
				</div>
			</div>
			<div class='col-sm-1'></div>
			<div class='col-sm-3'>
				<div class='form-group'>
					<?= Html::label('Principal Approved', 'principal-approved-label', ['class' => 'control-label']) ?>
					<?= Html::input('text', "items[$key][Approved][principal]", money_format('%n', $sku['requested_refund_amount']['principal']), ['class'=>'form-control']);?>
				</div>
			</div>		
		</div>
		
		<div class='row'>
			<div class='col-sm-3'>
				<div class='form-group'>
					<?= Html::label('Tax Requested', 'tax-label', ['class' => 'control-label']) ?>
					<?= Html::input('text', "items[$key][Requested][tax]", money_format('%n',$sku['requested_refund_amount']['tax']), ['readonly' => true, 'class'=>'form-control']);?>
				</div>
			</div>
			<div class='col-sm-1'></div>
			<div class='col-sm-3'>
				<div class='form-group'>
					<?= Html::label('Tax Approved', 'tax-approved-label', ['class' => 'control-label']) ?>
					<?= Html::input('text', "items[$key][Approved][tax]", money_format('%n',$sku['requested_refund_amount']['tax']), ['class'=>'form-control']);?>
				</div>
			</div>
		</div>
		<div class='row'>
			<div class='col-sm-3'>
				<div class='form-group'>
					<?= Html::label('Shipping Cost Requested', 'shipping_cost-label', ['class' => 'control-label']) ?>
					<?= Html::input('text', "items[$key][Requested][shipping_cost]", money_format('%n',$sku['requested_refund_amount']['shipping_cost']), ['readonly' => true, 'class'=>'form-control']);?>
				</div>
			</div>
			<div class='col-sm-1'></div>
			<div class='col-sm-3'>
				<div class='form-group'>
					<?= Html::label('Shipping Cost Approved', 'shipping_cost-approved-label', ['class' => 'control-label']) ?>
					<?= Html::input('text', "items[$key][Approved][shipping_cost]", money_format('%n',$sku['requested_refund_amount']['shipping_cost']), ['class'=>'form-control']);?>
				</div>
			</div>
		</div>
		<div class='row'>
			<div class='col-sm-3'>
				<div class='form-group'>
					<?= Html::label('Shipping Tax Reqeusted', 'shipping_tax-label', ['class' => 'control-label']) ?>
					<?= Html::input('text', "items[$key][Requested][shipping_tax]", money_format('%n',$sku['requested_refund_amount']['shipping_tax']), ['readonly' => true, 'class'=>'form-control']);?>
				</div>
			</div>
			<div class='col-sm-1'></div>
			<div class='col-sm-3'>
				<div class='form-group'>
					<?= Html::label('Shipping Tax Approved', 'shipping_tax-approved-label', ['class' => 'control-label']) ?>
					<?= Html::input('text', "items[$key][Approved][shipping_tax]", money_format('%n',$sku['requested_refund_amount']['shipping_tax']), ['class'=>'form-control']);?>
				</div>
			</div>
		</div>
		<div class='row'>
			<div class='col-sm-7'>
				<div class='form-group'>
					<?= Html::label('If you do not return the full amount, you need to select one of these reasons:', 'reason-codes', ['class' => 'control-label']) ?>
					<?= Html::dropDownList("items[$key][Approved][feedback]", '', $reasonArray, ['class'=>'form-control']) ?>	
				</div>
			</div>	
		</div>

		<div class='row'>
			<div class='form-group'>
				<div class='col-sm-7'>
					<?= Html::textarea("items[$key][Approved][notes]", '', ['class'=>'form-control', 'rows' => 3, 'cols' => 100, 'placeHolder'=>'notes'])?>
				</div>
			</div>
		</div>
	<?php 
	}
	?>

	<div class='row' style="padding:20px;">
		<div class='col-sm-3'>
			<div class='form-group'>
				<?= Html::Button('Complete', ['class' => 'btn btn-primary', id=>'complete']);?>
				<?= Html::Button('Reject', ['class' => 'btn btn-primary', 'id'=>'reject']);?>
			</div>
		</div>
	</div>
		
	<?= Html::endForm(); ?>
	
	<?php 
} catch (Exception $e){
	Yii::error("Error : ".$e->getMessage(), "shopify.return.order.error");
}
?>

<script>
$(document).ready(function(){
	$('#reject-ajax').hide();
	$('#image').hide();
	$('#reject').click(function(e){
		$('#reject-ajax').show();
	})

	$('#cancel').click(function(e){
		$('#reject-ajax').hide();
	})

	$('#reject-return').click(function(e){
		$.ajax({
		    type: 'POST',
		    url: '/ajax-return-complete?action=reject', 
		    data: $("#return-form").serialize(),
		    success: function(msg){
		        alert(msg);
		        window.location = window.history.go(-1); 
		        $('#image').hide();
		        return false;
		    },
		    beforeSend : function (){
		    	$('#image').show();
            },
            complete: function(){
                $('#image').hide();
            },
		})
	})

	$('#complete').click(function(e){
		$.ajax({
		    type: 'POST',
		    url: '/ajax-return-complete', 
		    data: $("#return-form").serialize(),
		    success: function(msg){
		        alert(msg);
		        window.location = window.history.go(-1); 
		        $('#image').hide();
		        return false;
		    },
		    beforeSend : function (){
		    	$('#image').show();
            },
            complete: function(){
                $('#image').hide();
            },
		})
	})
});
</script>