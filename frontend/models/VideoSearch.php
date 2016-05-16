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
use yii\db\ActiveQuery;

class VideoSearch extends Video
{

    public $viewsSearchType = '>';
    public $likesSearchType = '>';
    public $dislikesSearchType = '>';
    public $uploadedSearchType = '>';

    public function search($params){
        $query = self::find();

        $this->addCondition($query, 'name', true);

        if(!empty($params['views']) && isset($this->operands[$this->viewsSearchType])){
            $query->andWhere("`views` {$this->operands[$this->viewsSearchType]} '{$params['views']}'");
        }

        if(!empty($params['likes']) && isset($this->operands[$this->likesSearchType])){
            $query->andWhere("`likes` {$this->operands[$this->likesSearchType]} '{$params['likes']}'");
        }

        if(!empty($params['dislikes']) && isset($this->operands[$this->dislikesSearchType])){
            $query->andWhere("`dislikes` {$this->operands[$this->dislikesSearchType]} '{$params['dislikes']}'");
        }

        if(!empty($params['uploaded']) && isset($this->operands[$this->checkedSearchType])){
            $query->andWhere("`uploaded` {$this->operands[$this->checkedSearchType]} '{$params['uploaded']}'");
        }

        $dataProvider = new ActiveDataProvider([
            'query'     =>  $query,
            'pagination'    =>  [
                'pageSize'  =>  50
            ],
            'sort'  =>  [
                'defaultOrder'  =>  [
                    'views' =>  SORT_DESC
                ]
            ]
        ]);

        return $dataProvider;
    }

    public function getOperands(){
        return [
            'equal'     =>  '=',
            'more'      =>  '>',
            'less'      =>  '<'
        ];
    }

    public function rules(){
        return [
            [['likes', 'views', 'dislikes'], 'integer'],
            [['viewsSearchType', 'likesSearchType', 'dislikesSearchType', 'uploadedSearchType'], 'safe'],
            [['name', 'likes', 'views', 'dislikes', 'uploaded', 'channelID', 'categoryID', 'liveBroadcast'], 'safe']
        ];
    }

    /**
     * @param ActiveQuery $query
     * @param string $attribute
     * @param bool $partialMatch
     */
    protected function addCondition($query, $attribute, $partialMatch = false) {
        $value = $this->$attribute;
        if (trim($value) === '') {
            return;
        }

        if ($partialMatch) {
            $query->andWhere(['like', $attribute, $value]);
        }else{
            $query->andWhere([$attribute => $value]);
        }
    }

}