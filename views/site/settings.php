<?php

/* @var $this yii\web\View */
use app\models\Shopify;
use yii\web\View;

$this->title = 'Settings';
$request = Yii::$app->request;
$requestShopify = $request->post('Jet');

//Update Jet Settings
$updateJetFlag = $request->post('updateJetFlag');
if (isset($updateJetFlag)){
	//Store API KEYs to DB
	$shopify_init->jet_api_key = $requestShopify['jet_api_key'];
	$shopify_init->jet_pass = $requestShopify['jet_pass'];
	$shopify_init->save();
	$updateJetFlag = '';
	$message = "Your Jet settings have been saved.";
	if ($shopify_init->Setup =="Add Settings"){
		$result = $shopify_init->connectJet("true");
		$message .= "<br>" . json_encode($result);
		if ($result){
			$setup = $shopify_init->progressSetup();
			echo $setup;
		}
	}
}

//Test Jet Connection
$testJetFlag = $request->get('testJetFlag');
if (isset($testJetFlag)){
	$result = $shopify_init->connectJet("true");
	$message = $result;
}

//Display the message if set
if (isset($message)){
	echo "<pre>";
	print_r( $message);
	echo "</pre>";
}
?>
<div class="site-index">
    <div class="body-content">
        <div class="row">
       		<h2>Settings</h2>
        	<table border="1" width="100%">
        		<tr>
        			<?php 
        			// *********************** JET API SETTINGS ***********************
        			echo $this->render('_settings-jet-api-keys', ['shopify_init'=>$shopify_init]); 
        			?>
        		</tr>
        		<tr>
        			<?php 
        			// *********************** JET Fulfillment SETTINGS ***********************
        			echo $this->render('_settings-jet-fulfillment-node-details', ['shopify_init'=>$shopify_init]); 
        			?>
        		</tr>
        		<tr>
        			<?php 
        			// *********************** Webhook SETTINGS ***********************
        			echo $this->render('_settings-webhooks', ['shopify_init'=>$shopify_init]); 
        			?>
        		</tr>
        	</table>

        </div>

    </div>
</div>

