<?php

namespace common\models;

use frontend\helpers\ParseHelper;
use Yii;

/**
 * This is the model class for table "videos".
 *
 * @property string $link
 * @property string $name
 * @property string $views
 * @property string $likes
 * @property string $dislikes
 * @property string $uploaded
 * @property string $checked
 * @property string $link_hash
 * @property string $added
 * @property integer $id
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
    
    public function getYoutubeID(){
        return preg_replace('/(.*)\?v=/', '', $this->link);
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

    public static function findByLinkHash($link_hash, $result = true){
        $return = self::find()->where(['link_hash' => $link_hash]);

        if($result){
            return $return->one();
        }

        return $return;
    }

    public function getNext(){
        if(empty($this->_next)){
            $this->_next = self::find()->where('views <= '.$this->views)->andWhere("`link_hash` != '{$this->link_hash}'")->orderBy('views DESC')->limit(1)->one();
        }

        return $this->_next;
    }

    public function getPrevious(){
        if(empty($this->_previous)){
            $this->_previous = self::find()->where('views >= '.$this->views)->andWhere("`link_hash` != '{$this->link_hash}'")->orderBy('views ASC')->limit(1)->one();
        }

        return $this->_previous;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['views', 'likes', 'dislikes', 'categoryID'], 'integer'],
            [['checked'], 'safe'],
            [['added', 'uploaded'], 'date', 'format'    =>  'php:Y-m-d'],
            [['link', 'name', 'link_hash', 'channelID'], 'string', 'max' => 255],
            [['link', 'name'], 'trim'],
            [['liveBroadcast'], 'boolean'],
            [['link', 'name', 'views', 'likes', 'dislikes', 'uploaded', 'checked', 'added', 'channelID', 'categoryID'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'link'          => 'Ссылка',
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

    public function beforeSave($insert)
    {

        if($this->isNewRecord || empty($this->link_hash)){
            $this->link_hash = md5($this->link);
        }

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
