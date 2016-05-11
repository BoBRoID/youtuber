<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "workers".
 *
 * @property integer $id
 * @property integer $groupID
 */
class Worker extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'workers';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['groupID'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'groupID' => 'Group ID',
        ];
    }
}
