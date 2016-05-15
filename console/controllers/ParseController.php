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
use frontend\helpers\ParseHelper;
use frontend\models\YoutubeVideo;
use yii\console\Controller;
use yii\db\IntegrityException;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class ParseController extends Controller
{

    public function actionIndex($debug = false){
        $i = 0;

        $usedGroups = ArrayHelper::getColumn(Worker::find()->select('groupID')->distinct('groupID')->where('groupID != 0')->groupBy('groupID')->asArray()->all(), 'groupID');

        $availableGroups = ArrayHelper::getColumn(Link::find()->select('group')->distinct('group')->groupBy('group')->having('COUNT(`youtubeID`) > 0')->asArray()->all(), 'group');

        $group = array_rand(array_diff($availableGroups, $usedGroups));

        $worker = new Worker([
            'groupID' =>  $group
        ]);

        $worker->save(false);

        $links = Link::find()->where(['group' => $group]);

        $videosCount = $links->count();

        echo "   > Total videos: {$videosCount} \r\n";

        while($i != $videosCount){
            foreach($links->orderBy('added')->limit(50)->each() as $videoLink){
                $i++;
                $youtubeVideo = new YoutubeVideo(['link' => $videoLink->link]);

                if($debug){
                    echo "   > Video {$i} from {$videosCount}... ";
                }

                if((Video::find()->where(['youtubeID' => $videoLink->youtubeID])->count() >= 1) == false){
                    $parseTime = time() + microtime();

                    $youtubeVideo->parse();

                    $parseTime = (time() + microtime()) - $parseTime;

                    if($youtubeVideo->save(true)){
                        $videoLink->delete();
                    }

                    if($debug){
                        echo "Time spent: ".$parseTime." sec.\r\n";
                    }
                }else{
                    $videoLink->delete();
                    if($debug){
                        echo "video {$i} already in the database... \r\n";
                    }
                }

                if($i == $videosCount){
                    break;
                }
            }

            if($i == $videosCount){
                break;
            }
        }

        $worker->delete();
    }

    public function actionParseYoutubeKeys($debug = false){
        $videosCount = Video::find()->where('youtubeID = \'\' OR youtubeID is NULL')->orderBy('checked')->count();
        $i = 0;

        echo "   > Total videos: {$videosCount} \r\n";

        while($i != $videosCount){
            foreach(Video::find()->where('youtubeID = \'\' OR youtubeID is NULL')->orderBy('checked')->limit(1000)->each() as $video){
                $i++;
                $video->youtubeID = $video->getYoutubeID();

                if($debug){
                    echo "   > Video {$i} from {$videosCount}... ";
                }

                if($video->save(false) && $debug){
                    echo "Parsed!\r\n";
                }elseif($debug){
                    echo "Not parsed! Suggestion: \r\n";
                    //var_dump($video->getErrors());
                }

                if($i == $videosCount){
                    break;
                }
            }

            if($i == $videosCount){
                break;
            }
        }
    }

    public function actionParseLinksYoutubeKeys($debug = false){
        $videosCount = Link::find()->where('youtubeID = \'\' OR youtubeID is NULL')->count();
        $i = 0;

        echo "   > Total links: {$videosCount}";

        while($i != $videosCount){
            foreach(Link::find()->where('youtubeID = \'\' OR youtubeID is NULL')->limit(2000)->each() as $video){
                $i++;

                if($debug){
                    echo "   > Video {$i} from {$videosCount}... ";
                }

                $video->youtubeID = $video->getYoutubeID();

                if($video->save(false) && $debug){
                    echo "Parsed!\r\n";
                }elseif($debug){
                    echo "Not parsed! Suggestion: \r\n";
                    //var_dump($video->getErrors());
                }

                if($i == $videosCount){
                    break;
                }
            }

            if($i == $videosCount){
                break;
            }
        }
    }

    public function actionLinkParser($debug = false){
        $i = 0;

        $availableGroups = $usedGroups = [];

        echo "   > start working: ".date('H:i:s')."\r\n";

        if($debug){
            echo "   > select used groups...\r\n";
        }

        foreach(Worker::find()->select('groupID')->distinct('groupID')->where('groupID != 0')->all() as $worker){
              $usedGroups[] = $worker->groupID;
        }

        if($debug){
            echo "   > select available groups...\r\n";
        }

        foreach(Link::find()->select('group')->distinct('group')->andWhere(['not in', 'group', $usedGroups])->groupBy('group')->having('COUNT(`youtubeID`) > 0')->asArray()->all() as $groupID){
            $availableGroups[] = $groupID['group'];
        }

        $group = array_rand($availableGroups);

        $worker = new Worker([
            'groupID' =>  $group
        ]);

        $worker->save(false);


        if($debug){
            echo "   > getting links count...\r\n";
        }

        $links = Link::find()->where(['group' => $group]);

        $videosCount = $links->count();

        if($debug){
            echo "   > Total videos: {$videosCount} \r\n";
        }
        
        $addedVideos = 0;

        foreach($links->orderBy('added')->each() as $videoLink){
            $i++;
            $youtubeVideo = new YoutubeVideo(['link' => $videoLink->link]);
            if($debug){
                echo "   > Video {$i} from {$videosCount}... ";
            }

            $parseTime = time() + microtime();

            $youtubeVideo->parse();

            $parseTime = (time() + microtime()) - $parseTime;

            $relatedLinks = [];
            
            foreach($youtubeVideo->relatedLinks as $relatedLink){
                $relatedLinks[] = ParseHelper::parseYoutubeID($relatedLink);
            }

            $existedVideos = ArrayHelper::getColumn(Video::find()->select(['youtubeID'])->where(['in', 'youtubeID', $relatedLinks])->asArray()->all(), 'youtubeID');

            if($debug){
                $addedVideos = 0;
            }

            foreach(array_diff($relatedLinks, $existedVideos) as $youtubeID){
                $link = Link::find()->where(['youtubeID' => $youtubeID])->count();

                if($link <= 0){
                    $link = new Link(['youtubeID' => $youtubeID]);

                    try{
                        if($link->save()){
                            $addedVideos++;
                        }

                    }catch (IntegrityException $e){

                    }
                }
            }

            //$videoLink->delete();
            if($debug){
                echo "Links added: {$addedVideos}. Time spent: {$parseTime} sec.\r\n";
            }
        }

        echo "   > end working: ".date('H:i:s');

        if(!$debug){
            echo " Added {$addedVideos} video";
        }

        echo "\r\n";

        $worker->delete();
    }

    public function actionApiYoutubeParser($debug = false){
        $api = new YoutubeAPI();

        $i = 0;

        $availableGroups = $usedGroups = [];

        echo "   > start working: ".date('H:i:s')."\r\n";

        if($debug){
            echo "   > select used groups...\r\n";
        }

        foreach(Worker::find()->select('groupID')->distinct('groupID')->where('groupID != 0')->all() as $worker){
            $usedGroups[] = $worker->groupID;
        }

        if($debug){
            echo "   > select available groups...\r\n";
        }

        foreach(Link::find()->select('group')->distinct('group')->andWhere(['not in', 'group', $usedGroups])->groupBy('group')->having('COUNT(`youtubeID`) > 0')->asArray()->all() as $groupID){
            $availableGroups[] = $groupID['group'];
        }

        $group = array_rand($availableGroups);

        $worker = new Worker([
            'groupID' =>  $group
        ]);

        if($debug){
            echo "   > selected group: {$group}\r\n";
        }

        $worker->save(false);

        $almostLinks = Link::find()->where('`youtubeID` != \'\'')->andWhere(['group' => $group])->count();

        $i = 0;

        if($debug){
            echo "   > total links: {$almostLinks}\r\n";
        }

        while($almostLinks != $i) {
            foreach (Link::find()->where('`youtubeID` != \'\'')->andWhere(['group' => $group])->orderBy('added')->limit(500)->each() as $link) {
                $i++;

                if($debug){
                    echo "   > Video {$i} from {$almostLinks}...";
                }

                $video = new Video([
                    'youtubeID' => $link->youtubeID,
                ]);

                $apiData = $api->getVideos($link->youtubeID);

                try {
                    $video->applyApiData($apiData);

                    $video->save(false);

                    echo " Added!";
                } catch (NotFoundHttpException $e) {
                    echo " Deleted!";
                    $video->delete();
                } catch (IntegrityException $e) {
                    if ($e->getCode() == 23000) {
                        $video = Video::findOne(['youtubeID' => $link->youtubeID]);

                        if($video){
                            $video->applyApiData($apiData);
                            echo " Updated!";
                            $video->save(false);
                        }
                    }
                }

                $link->delete();

                if($debug){
                    echo "\r\n";
                }

                if ($i == $almostLinks) {
                    break;
                }
            }

            if ($i == $almostLinks) {
                break;
            }
        }
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
