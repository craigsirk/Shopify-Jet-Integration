<?php 
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Shopify;
use app\models\jetFulfillmentNodes;

?>
	<td style="padding:20px;"><p>Jet Settings</p></td>
	
    <td style="padding:20px;padding-left:30px;">
		<?php 
		$form = ActiveForm::begin([
			'id' => 'jet-add-form',
			'options' => ['class' => 'form-horizontal'],
			'method' => 'post',
			'action' => ['/settings'],
			'fieldConfig' => [
				'template' => "{label}\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-11\">{error}</div>",
				'labelOptions' => ['class' => 'col-lg-3 control-label'],
			],
		]);
		?>
		<?= Html::hiddenInput('updateJetFlag', "true"); ?>
		<?= $form->field($shopify_init, 'jet_api_key')->textInput(['autofocus' => true,'value' => $shopify_init->jet_api_key]) ?>
		<?= $form->field($shopify_init, 'jet_pass')->textInput(['value' => $shopify_init->jet_pass]) ?>
			        	
		<div class="form-group">
			<?= Html::submitButton('Set Jet Credentials', ['class' => 'btn btn-primary', 'name' => 'set-button']) ?>
			<?= Html::a('Test Jet Credentials', ['/settings', 'testJetFlag' => "true"], ['class' => 'btn btn-primary']) ?>
		</div>
	<?php ActiveForm::end(); ?>
	</td>