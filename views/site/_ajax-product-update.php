<?php

/* @var $this yii\web\View */
use app\models\Shopify;
use yii\helpers\Html;
use app\models\Products;

try {
	$get = Yii::$app->request;
	$product = Yii::$app->request->post('Products');
	$shopify_init = Shopify::find()->where(['shop' => $get->get('shop')])->one();


	$prod = new Products(); //Used to pull the product object hints/descriptions
	$namespace = "jet_settings";
	$meta = $shopify_init->getProductMetadata($product[id], $namespace);
	
	foreach ($product as $attribute => $value){
		//if $value is null, delete the metafield
		
		if (!$value){
			//Get the Metafield ID
			foreach ($meta['metafields'] as $key=>$metaValueArray){
				if ($metaValueArray[key]==$attribute){
					$metaID = $metaValueArray[id];
					break;
				}
			}
			if ($attribute=="product_description"){
				//default the description source to shopify, this will be overridden if a differnet product description
				$product_description_source = $shopify_init->postProductMetadata($product[id], $namespace, 'product_description_source', 'shopify', $prod->types['product_description_source'], substr($prod->getAttributeHint('product_description_source'),0,254));
				
			}
			$$attribute = $shopify_init->deleteProductMetadata($product[id], $metaID);
				
		} else {
			if ($attribute=="cpsia_cautionary_statements"){
				$value = implode(",", $product['cpsia_cautionary_statements']);
				$$attribute = $shopify_init->postProductMetadata($product[id], $namespace, $attribute, $value, $prod->types[$attribute], substr($prod->getAttributeHint($attribute),0,254));
			} else if ($attribute=="product_description"){
				$shopifyProduct = $shopify_init->getProducts($product[id]);
				if ($shopifyProduct['product_description'] != $value){
					$$attribute = $shopify_init->postProductMetadata($product[id], $namespace, $attribute, $value, $prod->types[$attribute], substr($prod->getAttributeHint($attribute),0,254));
					$product_description_source = $shopify_init->postProductMetadata($product[id], $namespace, 'product_description_source', 'custom', $prod->types['product_description_source'], substr($prod->getAttributeHint('product_description_source'),0,254));
				} 
			} else {
				$$attribute = $shopify_init->postProductMetadata($product[id], $namespace, $attribute, $value, $prod->types[$attribute], substr($prod->getAttributeHint($attribute),0,254));
			}
		}
	}
	echo "Your Product data has been updated!";
} catch (Exception $e){
	Yii::error("Error : ".$e->getMessage(), "shopify.product.update.error");
	
	print_r ($e->getMessage());
}
