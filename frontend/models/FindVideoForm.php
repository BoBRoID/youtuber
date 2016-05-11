<?php
/**
 * Created by PhpStorm.
 * User: bobroid
 * Date: 09.05.16
 * Time: 17:16
 */

namespace frontend\models;


use yii\base\Model;

/**
 * Class FindVideoForm
 * @package frontend\models
 * @property string $url
 */
class FindVideoForm extends Model
{

    public function rules()
    {
        return [
            [['url'], 'safe']
        ];
    }

    public $_url;

    public function setUrl($url){
        $this->_url = preg_replace('/&amp;(.*)/', '', $url);
    }

    public function getUrl(){
        return $this->_url;
    }

    public function attributeLabels()
    {
        return [
            'url'   =>  'Адрес видео: '
        ];
    }

}