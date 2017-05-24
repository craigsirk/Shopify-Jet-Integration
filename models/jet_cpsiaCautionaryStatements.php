<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

/**


 */

class jet_cpsiaCautionaryStatements extends ActiveRecord
{
	//Shopify Values
	//public $id;
	//public $Taxcode;
	
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'jet_cpsiaCautionaryStatements';
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
}
