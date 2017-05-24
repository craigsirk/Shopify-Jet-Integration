<?php

/*
 * This is the main page for the embedded app.
 */
/* @var $this yii\web\View */
use yii\helpers\Html;
use app\models\Shopify;

$session = Yii::$app->session;

$this->title = 'Home';

?>
<?php 
if ($shopify_init->Setup != "Complete"){
	$step = app\models\jet_SetupSteps::find()->where(['step'=>$shopify_init->Setup])->one();
	$max = app\models\jet_SetupSteps::find()->orderBy(['stepOrder' => SORT_DESC])->one();
	$message = "You have not yet completed the JET API setup.  You are currently on step <b>#" . $step->stepOrder . " - " . $step->step . "</b> of " . $max->stepOrder;
	
	$instructions = $step->description;
	$instructions = str_replace("@@Shop", $session->get('shop'), $instructions);
	echo "<pre>";
	echo $message;
	echo "<h3>Steps</h3>";
	echo $instructions;
	echo "</pre>";
}
?>	
<div class="site-index">
    <div class="body-content">
        <div class="row">

			<div class="col-lg-4">
	        	<h2>Jet to Shopfiy Adapter</h2>
	        	<p>This is the homepage to the Jet to Shopify adapter.</p>
	       	</div>
	       	<div class="col-lg-4">
	        	<p>You are currently using <?= yii::$app->params['version'] ?>. Please donate to help make this product a more robust and complete experience.</p>
	       	</div>
	       	<div class="col-lg-4">
	       		<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="PA72SUT58WVQY">
					<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
					<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form>
	       		
	       	</div>
			
        </div>
		
    </div>
</div>