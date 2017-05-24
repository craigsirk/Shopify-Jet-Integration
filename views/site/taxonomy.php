<?php

/* @var $this yii\web\View */
use app\models\jet_Taxonomy;
use app\models\join_JetTaxonomy_ShopifyStores;
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\helpers\Html;
use yii\grid\ActionColumn;

$session = Yii::$app->session;
$this->title = 'Taxonomy';

//Get all Shopify Product types
$productTypes = array();


$products = $shopify_init->getAllProducts('product_type');
foreach ($products['products'] as $product){
	if($product['product_type']){
		$productTypes[] = $product['product_type'];
	}
}
$productTypes = array_unique($productTypes);
foreach ($productTypes as $key=>$type){
	$types[$key]['type']=$type;
	$node = join_JetTaxonomy_ShopifyStores::find()->where(['ShopifyType'=>$type])->andWhere(['shopifyStore'=>$session->get('shop')])->one();
	if($node){
		$types[$key]['jet_node_id']=$node->jet_Taxonomy['jet_node_id'];
		$types[$key]['jet_node_name']=$node->jet_Taxonomy['jet_node_name'];
		//Get full path name
		$tempName = '';
		$temp=$node->jet_Taxonomy['jet_node_path'];
		$tempArray = explode("/",$temp);
		foreach (array_reverse($tempArray) as $key2=>$value){
			if ((count($tempArray)-1) == $key2){
				$tempName .= "<b>" . $value . "</b>";
			} else {
				$tempName .= $value . ": ";
			}	
		}
		$types[$key]['jet_node_full_name']=$tempName;
		
	} else {
		$types[$key]['jet_node_id']='';
		$types[$key]['jet_node_name']='';
	}
}

//set Data provider for gridview
$dataProviderArray  = new ArrayDataProvider([
	'allModels' => $types,
	'pagination' => [
		'pageSize' => 10,
	],
	'sort' => [
		'attributes' => [
			'type',
			'jet_node_id',
			'jet_node_name',
			'jet_node_full_name'
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

<?= Html::beginForm(['taxonomy', 'action' => 'complete'], 'post', [
			'class' => 'form-horizontal',
			'id'=> 'jet-taxonomy-form' 
		]); ?>
  
<?= GridView::widget([
	'dataProvider' => $dataProviderArray,
	'id'=>'type',
	'toolbar'=>false,
	'panel'=>[
		'heading'=>false,
	],
	'columns' => [
		[
			'attribute'=>'type',
			'label'=>'Shopify Type',
			'headerOptions' => ['style' => 'width:15%'],
		],
		[
			'attribute'=>'jet_node_id',
			'headerOptions' => ['style' => 'width:15%'],
		],
		[
			'attribute'=>'jet_node_name',
			'format'=>'raw',
			'label'=>'Jet Taxonomy',
			'value'=>function($data){
				if ($data['jet_node_full_name']){
					$prop = 'initValueText';
					$initValue =$data['jet_node_full_name'];
				} else {
					$prop ='options';
					$initValue = ['placeholder' => 'Search for a taxonomy ...'];
				}
				return Select2::widget([
					$prop=>$initValue,
				    'name'=>$data['type'],
				    'attribute' => 'jet_node_name',
				    'pluginOptions' => [
				        'allowClear' => true,
				    	'minimumInputLength' => 3,
				    	'language' => [
				    		'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
				    	],
				    	'ajax' => [
				    		'url' => \yii\helpers\Url::to(['ajax-taxonomy-get']),
				    		'dataType' => 'json',
				    		'data' => new JsExpression('function(params) { return {q:params.term}; }')
				    	],
				    	'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
				    	'templateResult' => new JsExpression('function(taxonomy) { return taxonomy.text; }'),
				    	'templateSelection' => new JsExpression('function (taxonomy) { return taxonomy.text; }'),
				    		
				    ],
				]);
			}
		],
		
	],
	'showPageSummary' => false
	]);
?>

<?= Html::button('Save', ['class' => 'btn btn-primary', 'id'=>'save']);?>
<?= Html::endForm(); ?>


<script>
$('#image').hide();
$('#save').click(function(e){
	$.ajax({
	    type: 'POST',
	    url: '/ajax-taxonomy-save?shop=<?= $shopify_init->shop ?>', 
	    data: $("#jet-taxonomy-form").serialize(),
	    success: function(msg){
	    	window.location = window.location.pathname;
			$('#image').hide();
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
