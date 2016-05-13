<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "thumbnails".
 *
 * @property string $videoID
 * @property integer $size
 * @property string $link
 */
class Thumbnail extends \yii\db\ActiveRecord
{

    const SIZE_PREVIEW = '1';
    const SIZE_SMALL = '2';
    const SIZE_MEDIUM = '3';
    const SIZE_LARGE = '4';
    const SIZE_FULL = '5';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'thumbnails';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['videoID'], 'required'],
            [['videoID', 'size'], 'integer'],
            [['link'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'videoID' => 'Video ID',
            'size' => 'Size',
            'link' => 'Link',
        ];
    }
}
