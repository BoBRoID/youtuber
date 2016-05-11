<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "links".
 *
 * @property string $link
 * @property string $added
 * @property integer $group
 */
class Link extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'links';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['added', 'group'], 'safe'],
            [['link'], 'required'],
            [['link'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'link' => 'Link',
            'added' => 'Added',
            'group' => 'Group',
        ];
    }
}
