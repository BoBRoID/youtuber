<?php

namespace common\models;

use frontend\helpers\ParseHelper;
use Yii;

/**
 * This is the model class for table "videos".
 *
 * @property string $name
 * @property string $views
 * @property string $likes
 * @property string $dislikes
 * @property string $uploaded
 * @property string $checked
 * @property string $added
 * @property integer $id
 * @property string $youtubeID
 */
class Video extends \yii\db\ActiveRecord
{
    protected $_next;
    protected $_previous;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'videos';
    }

    /**
     * @param array $data
     */
    public function applyApiData($data){
        $this->setAttributes([
            'uploaded'      =>  \Yii::$app->formatter->asDate($data['snippet']['publishedAt'], 'php:Y-m-d'),
            'channelID'     =>  $data['snippet']['channelId'],
            'name'          =>  $data['snippet']['title'],
            'categoryID'    =>  $data['snippet']['categoryId'],
            'liveBroadcast' =>  ($data['snippet']['liveBroadcastContent'] != 'none') ? 1 : 0,
        ]);

        if(isset($data['statistics'])){
            if(isset($data['statistics']['viewCount'])){
                $this->views = $data['statistics']['viewCount'];
            }

            if(isset($data['statistics']['likeCount'])){
                $this->likes = $data['statistics']['likeCount'];
            }

            if(isset($data['statistics']['dislikeCount'])){
                $this->dislikes = $data['statistics']['dislikeCount'];
            }
        }

        if(isset($data['snippet']['thumbnails'])){
            foreach($data['snippet']['thumbnails'] as $size => $thumbnail){
                $thumbnailModel = new Thumbnail(['videoID' => $this->id]);

                switch($size){
                    case 'default':
                        $thumbnailModel->size = Thumbnail::SIZE_PREVIEW;
                        break;
                    case 'medium':
                        $thumbnailModel->size = Thumbnail::SIZE_SMALL;
                        break;
                    case 'high':
                        $thumbnailModel->size = Thumbnail::SIZE_MEDIUM;
                        break;
                    case 'standard':
                        $thumbnailModel->size = Thumbnail::SIZE_LARGE;
                        break;
                    default:
                    case 'maxres':
                        $thumbnailModel->size = Thumbnail::SIZE_FULL;
                        break;
                }

                $thumbnailModel->link = $thumbnail['url'];

                $thumbnailModel->save(false);
            }
        }
    }

    public static function findByYoutubeID($youtubeID, $result = true){
        $return = self::find()->where(['youtubeID' => $youtubeID]);

        if($result){
            return $return->one();
        }

        return $return;
    }

    public function getNext(){
        if(empty($this->_next)){
            $this->_next = self::find()->where('views <= '.$this->views)->andWhere("`youtubeID` != '{$this->youtubeID}'")->orderBy(['views' =>  SORT_DESC, 'likes' => SORT_DESC])->limit(1)->one();
        }

        return $this->_next;
    }

    public function getPrevious(){
        if(empty($this->_previous)){
            $this->_previous = self::find()->where('views >= '.$this->views)->andWhere("`youtubeID` != '{$this->youtubeID}'")->orderBy(['views' => SORT_ASC, 'likes' => SORT_DESC])->limit(1)->one();
        }

        return $this->_previous;
    }

    public function getLink(){
        return ParseHelper::getYoutubeLink($this->youtubeID);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['views', 'likes', 'dislikes', 'categoryID', 'id'], 'integer'],
            [['checked'], 'safe'],
            [['added', 'uploaded'], 'date', 'format'    =>  'php:Y-m-d'],
            [['name', 'channelID'], 'string', 'max' => 255],
            [['youtubeID'], 'string', 'max' => 11],
            [['name'], 'trim'],
            [['liveBroadcast'], 'boolean'],
            [['name', 'views', 'likes', 'dislikes', 'uploaded', 'checked', 'added', 'channelID', 'categoryID', 'youtubeID'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name'          => 'Название',
            'views'         => 'Просмотров',
            'likes'         => 'Лайков',
            'dislikes'      => 'Дизлайков',
            'uploaded'      => 'Загружено на youtube',
            'checked'       => 'Обновлено',
            'added'         => 'Добавлено в базу',
            'channelID'     =>  '',
            'categoryID'    =>  '',
            'liveBroadcast' =>  ''
        ];
    }

    public function init(){
        $this->id = self::find()->max('id') + 1;

        return parent::init();
    }

    public function beforeSave($insert)
    {
        if($this->isNewRecord){
            $this->added = date('Y-m-d');
        }

        if(empty($this->views)){
            $this->views = 0;
        }

        $this->name = ParseHelper::removeEmoji($this->name);


        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }
}
