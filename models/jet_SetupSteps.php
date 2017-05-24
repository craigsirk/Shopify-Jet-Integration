<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**


 */

class jet_SetupSteps extends ActiveRecord
{

	
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'jet_SetupSteps';
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
            'id' => 'step ID',
        	
        ];
    }
}
