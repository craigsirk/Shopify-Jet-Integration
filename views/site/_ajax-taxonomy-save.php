<?php  
use app\models\Jet;
use app\models\join_JetTaxonomy_ShopifyStores;

$session = Yii::$app->session;
$request = Yii::$app->request;
try {
	$shopify_init = Jet::find()->where(['shop' => $request->get('shop')])->one();
	foreach ($request->post() as $shopifyType=>$jetNodeId){
		if ($shopifyType !="_csrf"){
			if ($jetNodeId){
				// insert / update taxonomy /type relation
				$join = join_JetTaxonomy_ShopifyStores::find()->where(['shopifyStore'=>$shopify_init->shop])->andWhere(['ShopifyType'=>$shopifyType])->one();
				if (!$join){
					//add
					$join = new join_JetTaxonomy_ShopifyStores();
					$join->shopifyStore = $shopify_init->shop;
					$join->ShopifyType = $shopifyType;	
				}
				$join->JetTaxonomyId = $jetNodeId;
				if ($join->save()){
					echo "saved";
					if ($shopify_init->Setup == "Set Taxonomy"){
						$setup = $shopify_init->progressSetup();
						echo $setup;
					}
				} else {
					Yii::error('Error saving jet taxonomy to shopify type', "shopify.taxonomy.error");
				}
			}
		}
	}
} catch (Exception $e){
	Yii::error("Error : ".$e->getMessage(), "shopify.taxonomy.error");
}
?>