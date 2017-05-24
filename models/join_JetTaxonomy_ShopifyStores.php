<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

/**


 */

class join_JetTaxonomy_ShopifyStores extends ActiveRecord
{
	//Shopify Values
	//public $id;
	//public $Taxcode;
	
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'join_JetTaxonomy_ShopifyStores';
	}

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
        	];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
 	
        ];
    }
    public function getShopify(){
    	return $this->hasOne(Shopify::className(), ['shop' => 'ShopifyStore']);
    }
    public function getJet_Taxonomy(){
    	return $this->hasOne(jet_Taxonomy::className(), ['jet_node_id' => 'JetTaxonomyId']);
    }
    
}
