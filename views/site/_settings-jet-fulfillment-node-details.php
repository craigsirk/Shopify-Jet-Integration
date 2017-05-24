<?php 
use yii\helpers\Html;
use app\models\jet_FulfillmentNodes;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use kartik\editable\Editable;
use kartik\popover\PopoverX;


$addNodeContent = '<p class="text-justify">' .
		'<input type="text" id="newFulfillmentName" name="newFulfillmentName" placeholder="New node name">' .
		'<br><br>' .
		'<input type="text" id="newFulfillmentID" name="newFulfillmentID" placeholder="New node ID (from Jet)">' .
		'</p>';
//$jetFulfillmentNode = new jet_FulfillmentNodes();
$dataProviderArray  = new ActiveDataProvider([
		'query' => jet_FulfillmentNodes::find()->where(['ShopifyStore'=>$shopify_init->shop]),
		'pagination' => [
			'pageSize' => 10,
		],
		'sort' => [
			'attributes' => [
				'ShopifyStore',
				'FulfillmentNodeID',
				'FulfillmentName'
			],
		],
]);

?>
	<td style="padding:20px;">Jet Fulfillment Nodes</td>
	
	<td style="padding:20px;padding-left:30px;">
		
			        	
		<?= GridView::widget([
			'dataProvider' => $dataProviderArray,
			'id'=>'jet-add-ful-grid',
			'pjax'=>true,
			'toolbar'=> [
				['content'=>
						PopoverX::widget([
								'header' => 'Add New Fulfillment Node',
								'placement' => PopoverX::ALIGN_TOP_RIGHT,
								'content' => $addNodeContent,
								'footer' => Html::button('Submit', ['onclick'=>'addFulfillment()', 'class'=>'btn btn-sm btn-primary']),
								'toggleButton' => ['label'=>'<i class="glyphicon glyphicon-plus"></i>', 'class'=>'btn btn-success'],
						]) .
					Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['settings'], ['data-pjax'=>0, 'class'=>'btn btn-default', 'title'=>"Reset Grid"]) 
				],	
			],
			'panel'=>[
				'type'=>GridView::TYPE_PRIMARY,
				'heading'=>false,
			],
			'columns' => [
				[
					'class'=>'kartik\grid\EditableColumn',
					'attribute'=>'FulfillmentName',
					'editableOptions'=> [
						'inputType' => \kartik\editable\Editable::INPUT_TEXT,
						'formOptions'=>['action' => ['/ajax-fulfillment-update']], // point to the new action
					]
				],
				[
					'class'=>'kartik\grid\EditableColumn',
					'attribute'=>'FulfillmentNodeID',
					'editableOptions'=> [
							'inputType' => \kartik\editable\Editable::INPUT_TEXT,
							'formOptions'=>['action' => ['/ajax-fulfillment-update']], // point to the new action
					]		
				],
				[
					'class'=>'kartik\grid\ActionColumn',
					'template' => '{delete}',
					'buttons' => [
						'delete' => function ($url, $dataProviderArray) {
								return Html::a('<span class="glyphicon glyphicon-trash"></span>', 
										['/settings'], [
										'title' => 'Delete',
										'onclick'=>"deleteFulfillment('" . $dataProviderArray->FulfillmentNodeID . "')"
								]);
							}
					],
					'headerOptions'=>['class'=>'kartik-sheet-style'],
				],
			],
			'responsive'=>true,
			'hover'=>true
			
		]);?>
	</td>
<script>
	function deleteFulfillment(id){
		var txt;
		var r = confirm("Are you sure you want to delete this item?");
		if (r == true) {
			$.ajax({
			    type: 'POST',
			    url: '/ajax-fulfillment-delete?shop=<?= $shopify_init->shop ?>', 
			    data: {'node':id},
			    success: function(msg){
			    	if (msg){
						alert(msg);
					}
			        window.location = window.location.pathname;
			        $('#image').hide();
			    },
			    beforeSend : function (){
			    	$('#image').show();
	            },
	            complete: function(){
	                $('#image').hide();
	            },
			});
		} 
	}
	function addFulfillment(){
		var txt;
		$.ajax({
			type: 'POST',
			url: '/ajax-fulfillment-add?shop=<?= $shopify_init->shop ?>', 
			data: {'node':document.getElementById('newFulfillmentID').value,'name':document.getElementById('newFulfillmentName').value},
			success: function(msg){
				if (msg){
					alert(msg);
				}
				window.location = window.location.pathname;
				$('#image').hide();
			},
			beforeSend : function (){
				$('#image').show();
	  		},
	     	complete: function(){
	   			$('#image').hide();
	  		},
		}); 
	}
</script>