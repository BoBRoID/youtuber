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
use frontend\models\YoutubeVideo;
use yii\console\Controller;
use yii\db\Query;

class ParseController extends Controller
{

    public function actionIndex(){
        $i = 0;

        $availableGroups = [];

        foreach(Link::find()->groupBy('group')->all() as $groupID){
            $availableGroups[] = $groupID->group;
        }

        $group = array_rand($availableGroups);

        $worker = new Worker([
            'groupID' =>  $group
        ]);

        $worker->save(false);

        $videosCount = Link::find()->where(['group' => $group])->count();

        echo "   > Total videos: {$videosCount} \r\n";

        foreach(Link::find()->orderBy('added')->each() as $videoLink){
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