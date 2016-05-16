<?php
/**
 * Created by PhpStorm.
 * User: bobroid
 * Date: 16.05.16
 * Time: 14:25
 */

namespace frontend\models;


use common\models\Video;
use yii\data\ActiveDataProvider;

class VideoSearch extends Video
{

    public function search($params){
        return new ActiveDataProvider([
            'query' =>  self::find()
        ]);
    }

}