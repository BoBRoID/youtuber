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
use frontend\helpers\DateHelper;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\base\Model;
use yii\db\IntegrityException;

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

    public function parse(){
        $video = null;

        try{
            $video = SimpleHTMLDom::file_get_html($this->link);
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
            $this->name = strip_tags($node);
        }

        foreach($video->find(".watch-time-text") as $node){
            //$this->publishDate = DateHelper::parseDate(preg_replace('/^\D+\s/', '', strip_tags($node)));
            $this->publishDate = preg_replace('/^\D+\s/', '', strip_tags($node));
            echo $this->publishDate;
            die();
        }

        foreach($video->find("#watch-related a") as $node){
            $this->relatedLinks[] = 'https://youtube.com'.preg_replace('/&amp;(.*)/', '', $node->attr['href']);
        }

        foreach($video->find(".related-list-item a") as $node){
            $this->relatedLinks[] = 'https://youtube.com'.preg_replace('/&amp;(.*)/', '', $node->attr['href']);
        }

        return true;
    }

    /**
     * @param $video Video
     */
    public function loadVideo($video){
        $this->setAttributes([
            'link'          =>  $video->link,
            'name'          =>  $video->name,
            'views'         =>  $video->views,
            'likes'         =>  $video->likes,
            'dislikes'      =>  $video->dislikes,
            'publishDate'   =>  $video->uploaded,
        ], false);
    }

    public function save($consoleMode = false){
        foreach($this->relatedLinks as $relatedLink){
            $link = Link::findOne(['link' => $relatedLink]);

            if(!$link){
                $link = new Link(['link' => $relatedLink]);

                try{
                    $link->save();
                }catch (IntegrityException $e){

                }
            }
        }

        $videoModel = Video::findOne(['link' => $this->link]);

        if(!$videoModel){
            $videoModel = new Video();
        }

        $videoModel->setAttributes([
            'link'      =>  $this->link,
            'name'      =>  $this->name,
            'views'     =>  $this->views,
            'likes'     =>  $this->likes,
            'dislikes'  =>  $this->dislikes,
            'uploaded'  =>  $this->publishDate,
        ]);

        try{
            $videoModel->save();
        }catch (IntegrityException $e){
            var_dump($e);

            if($e->getCode() == 1062){
                $videoModel = Video::findOne(['link' => $this->link]);

                $videoModel->setAttributes([
                    'name'      =>  $this->name,
                    'views'     =>  $this->views,
                    'likes'     =>  $this->likes,
                    'dislikes'  =>  $this->dislikes,
                    'uploaded'  =>  $this->publishDate,
                ]);
            }elseif($consoleMode){
                return false;
            }
        }

        return $videoModel;
    }

}