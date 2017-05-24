<?php
# install.php?shop=example-shop.myshopify.com

use linslin\yii2\curl;
use app\models\Shopify;
/*
$shop = Yii::$app->request->headers->get('forwarded-request-uri');
$shop = explode("&", $shop);
foreach ($shop as $value) {
	$pos = strpos( $value, 'shop' );
	if ( $pos !== false) {
		$shop = substr($value, '5');
		break;
	}
}
*/
$shop = yii::$app->request->get('shop');
//******************************	
# Step #1: Guard: http://docs.shopify.com/api/authentication/oauth#verification
//******************************
isset($shop) or die ('Query parameter "shop" missing.');
preg_match('/^[a-zA-Z0-9\-]+.myshopify.com$/', $shop) or die('Invalid myshopify.com store URL.');

//******************************
# Step 2: http://docs.shopify.com/api/authentication/oauth#asking-for-permission
//******************************
if (!isset($_GET['code'])){
	$scope = array(
		'read_content', 
		'write_content', 
		'read_themes', 
		'write_themes', 
		'read_products', 
		'write_products', 
		'read_customers', 
		'write_customers', 
		'read_orders', 
		'write_orders', 
		'read_script_tags', 
		'write_script_tags', 
		'read_fulfillments', 
		'write_fulfillments', 
		'read_shipping', 
		'write_shipping'
	);
		
	$url ="https://" . $shop . "/admin/oauth/authorize?client_id=" . Yii::$app->params['shopify']['api_key'] . "&scope=" . implode(",",$scope) ."&redirect_uri=" . Yii::$app->params['shopify']['redirect'] . "&state=" . Yii::$app->params['shopify']['nonce'];
	header( 'Location: ' . $url) ;
}
//******************************	
# Step 3: http://docs.shopify.com/api/authentication/oauth#confirming-installation
//******************************
try {
	//Init curl
	$curl = new curl\Curl();
		
	$url = "https://" . $shop ."/admin/oauth/access_token";
	$fields = array(
		'client_id' => Yii::$app->params['shopify']['api_key'],
		'client_secret' => Yii::$app->params['shopify']['secret'],
		'code' => $_GET['code']
	);
	$fields_string ='';
	//url-ify the data for the POST
	foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
	rtrim($fields_string, '&');
		
	//open connection
	$ch = curl_init();
		
	//set the url, number of POST vars, POST data
	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_POST, count($fields));
	curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	//execute post
	$result = curl_exec($ch);

	//close connection
	curl_close($ch);
		
	$result = (json_decode($result, true));
	echo "<pre>";
	print_r ($result);
	echo "</pre>";
		
	//Add shop to DB
	if ($oldshopify = Shopify::find()->where(['shop' => $shop])->one()){
		$oldshopify->oauth_token =$result['access_token'];
		$oldshopify->save();
		$shopify =$oldshopify;
			
	} else {
		$newshopify = new Shopify();
		$newshopify->shop = $shop;
		$newshopify->oauth_token = $result['access_token'];
		$newshopify->Setup="Add Settings";
		$newshopify->save();
		$shopify = $newshopify;
	}
		
	echo '<br><br>App Successfully Installed!';
		
//******************************
// # Step 4: Setup Webhooks
//******************************
	
	//==== Webhook when orders are fulfilled
	$fulfillJetOrder = array (
		'webhook'=>array(
			'topic'=>'orders/fulfilled',
			'address'=>'https://shopify.craigsirk.com/webhook-fulfill-jet-order',
			'format'=>'json'
		)	
	);
	$fulfillJetOrder = json_encode($fulfillJetOrder);
	$result = $shopify->postWebhook($fulfillJetOrder);
	if ($result){
		echo "Orders\Fulfilled Webhook: Successful!<br>";
	} else {
		echo "Orders\Fulfilled Webhook: Error, see settings page<br>";
	}
		
	//==== Webhook when Fulfillment (tracking) is updated
	$fulfillUpdate= array (
		'webhook'=>array(
			'topic'=>'fulfillments/update',
			'address'=>'https://shopify.craigsirk.com/webhook-update-jet-order',
			'format'=>'json'
		)
	);
	$fulfillUpdate = json_encode($fulfillUpdate);
	$result = $shopify->postWebhook($fulfillUpdate);
	if ($result){
		echo "fulfillments/update Webhook: Successful!<br>";
	} else {
		echo "fulfillments/update Webhook: Error, see settings page<br>";
	}
		
//************** End Webhooks **************************		
	//redirect back to shopify
	?>
	<script>
		window.location = "	https://<?= $shop ?>/admin/apps/craigsirk-jet-adapter";
	</script>
	<?php 
	
} catch (Exception $e){
	# HTTP status code was >= 400 or response contained the key 'errors'
	Yii::error("Error : ".$e->getMessage(), "shopify.install.error");
	print_r("Error : " . $e->getMessage());
}

?>