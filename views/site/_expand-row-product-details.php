<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Shopify;
use app\models\Jet;
use app\models\jet_TaxCodes;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\jet_cpsiaCautionaryStatements;


$request = Yii::$app->request;
try {
	$shopify_init = Shopify::find()->where(['shop' => $request->get('shop')])->one();
	$productArray = $shopify_init->getProducts($request->post('expandRowKey'));
	//echo "<pre>";
	//print_r($productArray);
	//echo "</pre>";
	?>
	
	<?php $form = ActiveForm::begin([
		'id' => 'jet-' . $productArray->id,
		'options' => ['class' => 'form-horizontal'],
		'method' => 'post',
		'action' => ['ajax?shop=' . $request->get('shop') .'&id=' . $productArray->id],
		'enableAjaxValidation'=> true,
		'fieldConfig' => [
				'template' => "{label}<a href=\"http\">?</a>\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-11\">{error}</div>",
				'labelOptions' => ['class' => 'col-lg-3 control-label'],
		],
	]);
	?>
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
	<div style="padding:20px;">
		<div class="row">
			 <p class="lead">These values are taken from the Shopify Product and can be changed through the Product page.</p>
		</div>
		<div class="row">
			<div class="col-sm-4">
				<?= $form->field($productArray, 'id')->textInput(['readonly' => true]); ?>
			</div>
			<div class="col-sm-4">
				<?= $form->field($productArray, 'product_title')->textInput(['readonly' => true]); ?>
			</div>
			<div class="col-sm-4">
				<?= $form->field($productArray, 'brand')->textInput(['readonly' => true]); ?>
			</div>
		</div>
		
		<div class="row">
			<div class="col-sm-4">
				<?= $form->field($productArray, 'standard_product_code')->textInput(['readonly' => true]); ?>
			</div>
			<div class="col-sm-4">
				<?php $data = array ("GTIN-14"=>"GTIN-14", "EAN"=>"EAN", "ISBN-10"=>"ISBN-10", "ISBN-13"=>"ISBN-13", "UPC"=>"UPC"); ?>
				<?= $form->field($productArray, 'standard_product_code_type')->widget(Select2::classname(), [
    				'data' => $data,
					'value' => $productArray->standard_product_code_type,
    				'options' => [
    						'class'=>'show', 
    						'placeholder' => 'Select a Product Code Type'],
    				'pluginOptions' => [
        				'allowClear' => true
    				],
					'pluginLoading'=>false,
					'disabled'=>true
				]); ?>
			</div>
			<div class="col-sm-4">
				<?= $form->field($productArray, 'shipping_weight_pounds')->textInput(['readonly' => true]); ?>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">
				<?php if($productArray['product_description_source']=="shopify" || !$productArray['product_description_source']){
					?>
					<p>This description is taken from the Shopify Object.</p>
					<?php 
				} else {
					?>
					<p>This description is a custom description for Jet.  To revert to the default Shopify description, make this feild blank</p>
					<?php 
				}
				?>
				<?= $form->field($productArray, 'product_description')->textarea(['rows' => '6']) ?>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<table>
					<tr> 
						<td>Main Image:</td>
						<td><img src='<?=$productArray[main_image_url]?>' width='100px'/></td>
					</tr>
					<?php if ($productArray[alternate_images]){?>
					<tr>
						<td>Additional Images:</td>
						<td>
							<?php 
							 foreach ($productArray[alternate_images] as $image){
							 	echo "<img src='$image[image_url]' width='100px'/>";
							 }
							?>
						</td>
					</tr>
					<?php }?>
				</table>

			</div>
		</div>
	</div>
	<div style="padding:20px;">
		<div class="row">
			 <p class="lead">These values are required for JET integration.</p>
		</div>
		<div class="row">
			<div class="col-sm-4">
				<?= $form->field($productArray, 'manufacturer')->textInput(); ?>
			</div>
			<div class="col-sm-4">
				<?= $form->field($productArray, 'mfr_part_number')->textInput(); ?>
			</div>
			<div class="col-sm-4">
				<?= $form->field($productArray, 'multipack_quantity') ?>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-4">
				<?= $form->field($productArray, 'map_price')->textInput(); ?>
			</div>
			<div class="col-sm-4">
				<?= $form->field($productArray, 'map_implementation')->dropdownList([
	        			'101' => '101', 
	        			'102' => '102',
						'103' => '103'
	    			],
    			['prompt'=>'Select Map Implementation']); ?>
			</div>
			<div class="col-sm-4">
				<?= $form->field($productArray, 'ASIN')->textInput(); ?>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<?= $form->field($productArray, 'bullets')->textarea(['rows' => '5']); ?>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-4">
				<?= $form->field($productArray, 'jet_browse_node_id')->textInput(['readonly'=>true]); //Need to get a list of accepted values?>
			</div>
			<div class="col-sm-4">
				<?= $form->field($productArray, 'jet_browse_node_name')->textInput(['readonly'=>true]); //Need to get a list of accepted values?>
			</div>
			<div class="col-sm-4">
				
			</div>
		</div>
	</div>
	<div style="padding:20px;">
		<div class="row">
			 <p class="lead">These values are optional.</p>
		</div>
		<div class="row">
			<div class="col-sm-4">
				<?= $form->field($productArray, 'number_units_for_ppu')->textInput();?>
			</div>
			<div class="col-sm-4">
				<?= $form->field($productArray, 'type_of_unit_for_ppu')->textInput();?>
			</div>
			<div class="col-sm-4">
				<?= $form->field($productArray, 'amazon_item_type_keyword')->textInput();?>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-4">
				<?= $form->field($productArray, 'category_path')->textInput();?>
			</div>
			<div class="col-sm-4">
				<?= $form->field($productArray, 'prop_65')->dropdownList([
	        			'True' => 'True', 
	        			'False' => 'False',
	    			],
    			['prompt'=>'Select Prop 65 Compliance']); ?>
			</div>
			<div class="col-sm-4">
				
			</div>
		</div>
		<div class="row">
			<div class="col-sm-4">
				<?= $form->field($productArray, 'package_length_inches')->textInput();?>
			</div>
			<div class="col-sm-4">
				<?= $form->field($productArray, 'package_width_inches')->textInput();?>
			</div>
			<div class="col-sm-4">
				<?= $form->field($productArray, 'package_height_inches')->textInput();?>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-4">
				<?= $form->field($productArray, 'display_length_inches')->textInput();?>
			</div>
			<div class="col-sm-4">
				<?= $form->field($productArray, 'display_width_inches')->textInput();?>
			</div>
			<div class="col-sm-4">
				<?= $form->field($productArray, 'display_height_inches')->textInput();?>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<?= $form->field($productArray, 'legal_disclaimer_description')->textarea(['rows' => '5']); ?>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
			<?php $data = jet_cpsiaCautionaryStatements::find()->asArray()->select('cpsia_cautionary_statements')->all();
				$data = ArrayHelper::map($data, 'cpsia_cautionary_statements','cpsia_cautionary_statements');
				?>
				
				<?= $form->field($productArray, 'cpsia_cautionary_statements')->widget(Select2::classname(), [
    				'data' => $data,
					'value' => $productArray->cpsia_cautionary_statements,
    				'options' => [
    						'multiple'=> true,
    						'class'=>'show',  
    						'placeholder' => 'Select a CPSIA Cautionary Statements'],
    				'pluginOptions' => [
        				'allowClear' => true
    				],
					'pluginLoading'=>false
				]); ?>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-4">
				<?= $form->field($productArray, 'country_of_origin')->textInput();?>
			</div>
			<div class="col-sm-4">
				<?= $form->field($productArray, 'safety_warning')->textInput();?>
			</div>
			<div class="col-sm-4">
				
			</div>
		</div>
		<div class="row">
			<div class="col-sm-4">
				<?= $form->field($productArray, 'msrp')->textInput();?>
			</div>
			<div class="col-sm-4">
				<?php $data = jet_TaxCodes::find()->asArray()->select('TaxCode')->all();
				$data = ArrayHelper::map($data, 'TaxCode','TaxCode'); ?>
				
				<?= $form->field($productArray, 'product_tax_code')->widget(Select2::classname(), [
    				'data' => $data,
					'value' => $productArray->product_tax_code,
    				'options' => [
    						'class'=>'show', 
    						'placeholder' => 'Select a Taxcode'],
    				'pluginOptions' => [
        				'allowClear' => true
    				],
					'pluginLoading'=>false
				]); ?>
			</div>
			<div class="col-sm-4">
				<?= $form->field($productArray, 'jet_tax_code_recommendation')->textInput(['readonly'=>true]); //Need to get a list of accepted values?>
				
			</div>
		</div>
	</div>
	<div style="padding:20px;">
		<?= Html::Button('Update', ['class' => 'btn btn-primary', 'id'=>'update-'. $productArray->id]);?>
	</div>
	<?php ActiveForm::end(); ?>
<?php } catch (Exception $e){
	Yii::error("Error : ".$e->getMessage(), "shopify.product.error");
}
?>

<script>
$(document).ready(function(){
	$('#image-<?= $request->post('expandRowKey')?>').hide();
	$('#update-<?= $productArray->id ?>').click(function(e){
		$.ajax({
		    type: 'POST',
		    url: '/ajax-product-update?shop=<?= $request->get('shop') ?>', 
		    data: $("#jet-<?= $productArray->id ?>").serialize(),
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


 