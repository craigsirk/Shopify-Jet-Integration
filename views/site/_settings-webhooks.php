<?php 
use yii\helpers\Html;
use app\models\Shopify;
use yii\data\ArrayDataProvider;
use kartik\grid\GridView;


?>
	<td style="padding:20px;"><p>Web Hooks</p></td>
	
    <td style="padding:20px;padding-left:30px;">
		<?php
		$webhooks = $shopify_init->getWebhook(); 
		
		//Check that all webhooks exist
		foreach ($webhooks['webhooks'] as $key=>$value){
			if ($value['topic']=="orders/fulfilled"){
				$fulfilled = true;
			} else if ($value['topic']=="fulfillments/update"){
				$updated = true;
			} 
		}
		//Create Error message
		$message = "Need to create button to re-set webhooks<bR>";
		if (!$fulfilled){
			$message .= "Error: orders/fulfilled webhook missing!<br>";
		}
		if (!$updated){
			$message .= "Error: fulfillments/update webhook missing!<br>";
		}	
		
		
		//Create Data view grid
		$dataProviderArray  = new ArrayDataProvider([
				'allModels' => $webhooks['webhooks'],
				'pagination' => [
					'pageSize' => 10,
				],
				'sort' => [
					'attributes' => [
						'topic',
						'address',
						'format'
					],
			],
		]);
		?>
		
		<?= GridView::widget([
				'dataProvider' => $dataProviderArray,
				'id'=>'webhooks',
				'panel'=>[
					'type'=>GridView::TYPE_PRIMARY,
					'heading'=>false,
					'before'=>false
				],
				'columns' => [
					'topic',
					'address',
					'format'
				]								
		]);?>

	</td>