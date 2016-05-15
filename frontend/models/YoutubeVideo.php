<?php
/**
 * Created by PhpStorm.
 * User: bobroid
 * Date: 10.05.16
 * Time: 15:17
 */

namespace frontend\models;


use common\models\Link;
use common\models\Video;
use darkdrim\simplehtmldom\SimpleHTMLDom;
use frontend\helpers\ParseHelper;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\base\Model;
use yii\db\IntegrityException;
use yii\web\BadRequestHttpException;

class YoutubeVideo extends Model
{

    public $views = 0;

    public $likes = 0;

    public $dislikes = 0;

    public $relatedLinks = [];

    public $_link = '';

    public function setLink($link){
        $this->_link = preg_replace('/&amp;(.*)/', '', preg_replace('/www./', '', $link));
    }

    public function getLink(){
        return $this->_link;
    }

    public $publishDate = null;

    public $name = '';

    public $author = 0;

    public function rules(){
        return [
            [['views', 'likes', 'dislikes'], 'integer'],
            //[['views', 'likes', 'dislikes'], 'default' => 0],
            [['link', 'name', 'publishDate'], 'string'],
            [['views', 'likes', 'dislikes', 'link', 'publishDate', 'name', 'thumbnail'], 'safe']
        ];
    }

    public function getYoutubeID(){
        return ParseHelper::parseYoutubeID($this->link);
    }

    public function parse(){
        $video = null;

        try{
            $video = SimpleHTMLDom::file_curl_get_html($this->link);
        }catch (ErrorException $e){
            return false;
        }

        if(!$video){
            return false;
        }

        foreach($video->find(".like-button-renderer .like-button-renderer-like-button .yt-uix-button-content") as $node){
            $this->likes = preg_replace('/\D+/', '', $node);
        }

        foreach($video->find(".like-button-renderer .like-button-renderer-dislike-button .yt-uix-button-content") as $node){
            $this->dislikes = preg_replace('/\D+/', '', $node);
        }

        foreach($video->find(".watch-view-count") as $node){
            $this->views = preg_replace('/\D+/', '', $node);
        }

        foreach($video->find("#eow-title") as $node){
            $this->name = htmlspecialchars_decode(strip_tags($node));
        }

        foreach($video->find("[itemprop=datePublished]") as $node){
            $this->publishDate = \Yii::$app->formatter->asDate($node->attr['content'], 'php:Y-m-d');
        }

        foreach($video->find("#watch-related a") as $node){
            $this->relatedLinks[] = 'https://youtube.com'.preg_replace('/&amp;(.*)/', '', $node->attr['href']);
        }

        foreach($video->find(".related-list-item a") as $node){
            $this->relatedLinks[] = 'https://youtube.com'.preg_replace('/&amp;(.*)/', '', $node->attr['href']);
        }

        /*foreach($video->find("[itemprop=thumbnail] link[itemprop=url]") as $node){
            $this->thumbnail = $node->attr['href'];
        }*/

        return true;
    }
    
    public function parseAPI(){
        
    }

    /**
     * @param $video Video
     */
    public function loadVideo($video){
        $this->setAttributes([
            'link'          =>  $video->getLink(),
            'name'          =>  $video->name,
            'views'         =>  $video->views,
            'likes'         =>  $video->likes,
            'dislikes'      =>  $video->dislikes,
            'publishDate'   =>  $video->uploaded,
        ]);
    }

    public function save($consoleMode = false){
        foreach($this->relatedLinks as $relatedLink){
            $relatedLink = ParseHelper::parseYoutubeID($relatedLink);
            $link = Link::findOne(['youtubeID' => $relatedLink]);

            if(!$link){
                $link = new Link(['youtubeID' => $relatedLink]);

                try{
                    $link->save();
                }catch (IntegrityException $e){

                }
            }
        }

        $videoModel = Video::findOne(['youtubeID' => $this->youtubeID]);

        if(!$videoModel){
            $videoModel = new Video([
                'youtubeID' =>  $this->youtubeID
            ]);
        }

        $videoModel->setAttributes([
            'name'      =>  $this->name,
            'views'     =>  $this->views,
            'likes'     =>  $this->likes,
            'dislikes'  =>  $this->dislikes,
            'uploaded'  =>  $this->publishDate,
        ]);

        try{
            if(!$videoModel->save()){
                var_dump($videoModel->getErrors());
            }
        }catch (IntegrityException $e){
            echo " Video updated! ";

            /*if($e->getCode() == 1062){
                $videoModel = Video::findOne(['youtubeID' => ParseHelper::parseYoutubeID($this->link)]);

                $videoModel->setAttributes([
                    'name'      =>  $this->name,
                    'views'     =>  $this->views,
                    'likes'     =>  $this->likes,
                    'dislikes'  =>  $this->dislikes,
                    'uploaded'  =>  $this->publishDate,
                ]);
            }elseif($consoleMode){
                return false;
            }*/
        }

        return $videoModel;
    }

}