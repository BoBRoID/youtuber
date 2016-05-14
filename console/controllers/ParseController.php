<?php
/**
 * Created by PhpStorm.
 * User: bobroid
 * Date: 10.05.16
 * Time: 15:46
 */

namespace console\controllers;


use common\models\Link;
use common\models\Video;
use common\models\Worker;
use frontend\components\YoutubeAPI;
use frontend\helpers\DateHelper;
use frontend\models\YoutubeVideo;
use yii\console\Controller;
use yii\db\IntegrityException;
use yii\db\Query;
use yii\web\NotFoundHttpException;

class ParseController extends Controller
{

    public function actionIndex(){
        $i = 0;

        $availableGroups = $usedGroups = [];

        foreach(Worker::find()->where('groupID != 0')->groupBy('groupID')->all() as $worker){
            $usedGroups[] = $worker->groupID;
        }

        foreach(Link::find()->andWhere(['not in', 'group', $usedGroups])->groupBy('group')->having('COUNT(`link`) > 0')->all() as $groupID){
            $availableGroups[] = $groupID->group;
        }

        $group = array_rand($availableGroups);

        $worker = new Worker([
            'groupID' =>  $group
        ]);

        $worker->save(false);

        $links = Link::find()->where(['group' => $group]);

        $videosCount = $links->count();

        echo "   > Total videos: {$videosCount} \r\n";

        foreach($links->orderBy('added')->each() as $videoLink){
            $i++;
            $youtubeVideo = new YoutubeVideo(['link' => $videoLink->link]);
            echo "   > Video {$i} from {$videosCount}... ";

            if((Video::find()->where(['link' => $videoLink->link])->count() >= 1) == false){
                $parseTime = time() + microtime();

                $youtubeVideo->parse();

                $parseTime = (time() + microtime()) - $parseTime;

                if($youtubeVideo->save(true)){
                    $videoLink->delete();
                }


                echo "Time spent: ".$parseTime." sec.\r\n";
            }else{
                $videoLink->delete();
                echo "video {$i} already in the database... \r\n";
            }
        }

        $worker->delete();
    }

    public function actionParseYoutubeKeys(){
        $videosCount = Video::find()->where('youtubeID = \'\' OR youtubeID is NULL')->orderBy('checked')->count();
        $i = 0;

        foreach(Video::find()->where('youtubeID = \'\' OR youtubeID is NULL')->orderBy('checked')->each() as $video){
            $i++;
            echo "   > Video {$i} from {$videosCount}... ";
            $video->youtubeID = $video->getYoutubeID();

            if($video->save(false)){
                echo "Parsed!";
            }else{
                echo "Not parsed! Suggestion: ";
                var_dump($video->getErrors());
            }

            echo "\r\n";
        }
    }

    public function actionParseLinksYoutubeKeys($debug = false){
        if($debug){
            $videosCount = Link::find()->where('youtubeID = \'\' OR youtubeID is NULL')->count();
            $i = 0;
        }

        echo "   > Total links: {$videosCount}";

        for($group = 0; $group < $videosCount / 10000; $group++){
            foreach(Link::find()->where('youtubeID = \'\' OR youtubeID is NULL')->orderBy('added')->limit(10000)->each() as $video){
                if($debug){
                    $i++;
                    echo "   > Video {$i} from {$videosCount}... ";
                }

                $video->youtubeID = $video->getYoutubeID();

                if($video->save(false) && $debug){
                    echo "Parsed!";
                }elseif($debug){
                    echo "Not parsed! Suggestion: ";
                    var_dump($video->getErrors());
                }

                if($debug){
                    echo "\r\n";
                }
            }
        }
    }

    public function actionLinkParser(){
        $i = 0;

        $availableGroups = $usedGroups = [];

        foreach(Worker::find()->where('groupID != 0')->groupBy('groupID')->all() as $worker){
            $usedGroups[] = $worker->groupID;
        }

        foreach(Link::find()->andWhere(['not in', 'group', $usedGroups])->groupBy('group')->having('COUNT(`link`) > 0')->all() as $groupID){
            $availableGroups[] = $groupID->group;
        }

        $group = array_rand($availableGroups);

        $worker = new Worker([
            'groupID' =>  $group
        ]);

        $worker->save(false);

        $links = Link::find()->where(['group' => $group]);

        $videosCount = $links->count();

        echo "   > Total videos: {$videosCount} \r\n";

        foreach($links->orderBy('added')->each() as $videoLink){
            $i++;
            $youtubeVideo = new YoutubeVideo(['link' => $videoLink->link]);
            echo "   > Video {$i} from {$videosCount}... ";

            $parseTime = time() + microtime();

            $youtubeVideo->parse();

            $parseTime = (time() + microtime()) - $parseTime;

            $relatedLinks = [];
            
            foreach($youtubeVideo->relatedLinks as $relatedLink){
                $relatedLinks[] = preg_replace('/(.*)\?v=/', '', $relatedLink);
            }
            
            echo implode('", "', $relatedLinks).'"';
            die();

            foreach($youtubeVideo->relatedLinks as $relatedLink){
                $link = Link::find()->from('links, videos')->where(['or', ['`links`.`link`' => $relatedLink, '`videos`.`link`' => $relatedLink]])->count();

                if($link <= 0){
                    $link = new Link(['link' => $relatedLink]);

                    try{
                        $link->save();
                    }catch (IntegrityException $e){

                    }
                }
            }

            $videoLink->delete();
            $count = count($youtubeVideo->relatedLinks);

            echo "Links added: {$count}. Time spent: ".$parseTime." sec.\r\n";
        }

        $worker->delete();
    }

    public function actionApiReparser(){
        $api = new YoutubeAPI();

        foreach(Video::find()->where('youtubeID != \'\' AND youtubeID is NOT NULL')->orderBy('checked')->each(10) as $video){
            try{
                $video->applyApiData($api->getVideos($video->youtubeID));

                $video->save(false);
            }catch (NotFoundHttpException $e){
                $video->delete();
            }
        }
    }

    public function actionReparseDates(){
        $videosCount = Video::find()->count();
        $i = 0;

        foreach(Video::find()->each() as $video){
            $i++;

            echo "Video {$i} from {$videosCount}...";

            if(!strtotime($video->uploaded)){
                $video->uploaded = DateHelper::parseDate($video->uploaded);
            }

            if($video->save()){
                echo " saved!";
            }else{
                echo " not saved!";
            }

            echo "\r\n";
        }
    }

    public function actionGroupLinks(){
        $linksCount = Link::find()->where(['group' => 0])->count();
        $linksRequired = $linksCount;

        $lastGroup = Link::find()->select('MAX(`group`)')->scalar();

        $groupsCount = $lastGroup + round($linksCount / 1000) - 1;

        while($linksRequired > 0){
            $lastGroup++;
            echo "   > Group {$lastGroup} from {$groupsCount}... \r\n";

            $connection = \Yii::$app->db;
            $connection->createCommand("UPDATE `links` SET `group` = '{$lastGroup}' WHERE `group` = 0 ORDER BY `added` LIMIT 1000")->execute();

            $linksRequired = $linksRequired - 1000;
        }
    }

    public function actionReparse(){
        $i = 0;
        $videosCount = Video::find()->count();

        echo "   > Total videos: {$videosCount} \r\n";

        foreach(Video::find()->orderBy('checked')->each() as $video){
            $i++;
            echo "   > Video {$i} from {$videosCount}... ";
            $parseTime = time() + microtime();

            $youtubeVideo = new YoutubeVideo();
            
            $youtubeVideo->loadVideo($video);

            $youtubeVideo->parse();

            $parseTime = (time() + microtime()) - $parseTime;

            if(!$youtubeVideo->save()){
                echo " Video {$i} don't saved in the database... \r\n";
            }

            echo "Time spent: ".$parseTime." sec.\r\n";
        }
    }

}
