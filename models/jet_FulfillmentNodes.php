<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use app\models\Shopify;

/**
 *  shopify model, holds all the shopify API calls
 *  @property strong FulfillmentNodeID
 *

 */
class jet_FulfillmentNodes extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
    	return 'jet_FulfillmentNodes';
    }
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
    	return [
    			TimestampBehavior::className(),
    	];
    }
    
    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
    	return [
    			'FulfillmentNodeID' => "Fulfillment Node ID's",
    			'FulfillmentName' => "Fulfillment Node Name"
    	];
    }
    
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
    	return [
    			[['ShopifyStore', 'FulfillmentNodeID'], 'required' ]
    	];
    }
    public function getShopify(){
    	return $this->hasOne(Shopify::className(), ['shop' => 'ShopifyStore']);
    }
}