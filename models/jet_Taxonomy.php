<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use app\models\join_JetTaxonomy_ShopifyStores;

/**


 */

class jet_Taxonomy extends ActiveRecord
{
	//Shopify Values
	//public $id;
	//public $Taxcode;
	
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'jet_Taxonomy';
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
            'id' => 'Product ID',
        	
        ];
    }
    public function getJoin_JetTaxonomy_ShopifyStores(){
    	return $this->hasOne(join_JetTaxonomy_ShopifyStores::className(), ['JetTaxonomyId' => 'jet_node_id']);
    }
}
