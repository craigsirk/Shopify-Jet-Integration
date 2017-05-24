<?php
/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\models\Shopify;
use app\models\Products;
use app\models\Jet;

/*==============================================================
 * Error handler to catch all the PHP errors, warnings,....
 ============================================================== */
function customErrorHandling($errno, $errstr) {
	throw new Exception("Error: [$errno] $errstr");
}

//Sets a user-defined error handler function
set_error_handler("customErrorHandling", E_ALL);
$this->beginPage() ;
try {
	AppAsset::register($this);
	$session = Yii::$app->session;
	?>
	
	
	<!DOCTYPE html>
	<html lang="<?= Yii::$app->language ?>">
	<head>
	
	    <meta charset="<?= Yii::$app->charset ?>">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <?= Html::csrfMetaTags() ?>
	    <title><?= Html::encode($this->title) ?></title>
	    <?php $this->head() ?>
	    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.0/jquery.js"></script>
	    <script src="https://cdn.shopify.com/s/assets/external/app.js"></script>
	    <?php 
			//This is the javascript that redirects to Shopfiy
			//https://help.shopify.com/api/sdks/merchant-apps/embedded-app-sdk/methods#shopifyapp-init-config
		?>
	    <script type="text/javascript">
	    	ShopifyApp.init({
	      		apiKey: '<?php echo Yii::$app->params['shopify']['api_key'] ?>',
	      		shopOrigin: 'https://<?php echo $session->get('shop') ?>',
	      		debug: false,
	      	  	forceRedirect: true
	    	});
		</script>
		<script type="text/javascript">
		  ShopifyApp.ready(function(){
			  ShopifyApp.Bar.initialize({
				  buttons: {
				    primary: {
				      label: "Settings", 
				      href: "<?php echo Yii::$app->params['homeURL'] ?>/settings", target: "app",
				      loading: false
				    },
				    secondary: [
				      { label: "Help", href: "<?php echo Yii::$app->params['homeURL'] ?>/help", target: "app" },
				      { label: "Products",
				        type: "dropdown",
				        links: [
				                 { label: "Manage", href: "<?php echo Yii::$app->params['homeURL'] ?>/products", target: "app",loading: false },
				                 { label: "Price", href: "<?php echo Yii::$app->params['homeURL'] ?>/price", target: "app",loading: false },
				                 { label: "Inventory", href: "<?php echo Yii::$app->params['homeURL'] ?>/inventory", target: "app",loading: false },
				                 { label: "Taxonomy", href: "<?php echo Yii::$app->params['homeURL'] ?>/taxonomy", target: "app",loading: false }
				                 
				               ]
				      },
				      { label: "Orders",
					       type: "dropdown",
					       links: [
					           { label: "Manage", href: "<?php echo Yii::$app->params['homeURL'] ?>/orders2", target: "app",loading: false },
					       ]
					  },
					  { label: "Returns",
						   type: "dropdown",
						   links: [
						       { label: "Manage", href: "<?php echo Yii::$app->params['homeURL'] ?>/returns", target: "app",loading: false },						       
						       ]
					  },
				      { label: "Jet Account", href: "https://partner.jet.com/dashboard", target: "new" }
				    ],
				  },
				  title: '<?php echo $this->title ;?>',
				  icon: '<?php echo Yii::$app->params['homeURL'] ?>/assets/icon-jet.png',
				});
			  ShopifyApp.Bar.loadingOff();
		  });
		</script>
	</head>
	<body>
	
	<?php $this->beginBody() ?>

	<div class="wrap"> 
	    <div class="container-shopify">
	        <?= Breadcrumbs::widget([
	            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
	        ]) ?>
	        <?= $content ?>
	    </div>
	</div>
	
	<footer class="footer">
	    <div class="container">
	   	 	<p class="pull-left"><?= Yii::$app->params['version']?>&nbsp;</p> 
	   	 	
	        <p class="pull-left">&copy; Craigsirk <?= date('Y') ?></p>
	
	        <p class="pull-right"><?= Yii::powered() ?></p>
	    </div>
	</footer>
	
	<?php $this->endBody() ?>
	</body>
	</html>
	<?php $this->endPage() ?>
<?php 
} catch (Exception $e){
	Yii::error("Error : ".$e->getMessage(), "shopify.app.error");
}
?>