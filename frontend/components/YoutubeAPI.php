<?php
/**
 * Created by PhpStorm.
 * User: bobroid
 * Date: 13.05.16
 * Time: 11:39
 */

namespace frontend\components;


use linslin\yii2\curl\Curl;
use yii\base\Component;
use yii\base\ErrorException;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;

class YoutubeAPI extends Component
{

    public $youtubeApiURL = 'https://www.googleapis.com/youtube/v3/';

    public $myApiKey = 'AIzaSyCGEB3DcUpY-h7ePlCBvuzI44iMqc-h2Fg';

    /**
     * @param integer[] $IDs
     * @return array
     * @throws ErrorException
     * @throws NotFoundHttpException
     */
    public function getVideos($IDs){
        if(empty($IDs)){
            throw new ErrorException("Как парсить видео, если IDшников нет?");
        }

        $listLength = 1;

        if(is_array($IDs)){
            if(sizeof($IDs) > 50){
                $IDs = array_slice($IDs, 0, 50);
            }

            $listLength = sizeof($IDs);

            $IDs = implode(',', $IDs);
        }

        $response = $this->sendRequest([
            'action'    =>  'videos',
            'part'      =>  'snippet,statistics',
            'id'        =>  $IDs,
            'maxResults'=>  $listLength
        ]);

        if(empty($response['items'])){
            throw new NotFoundHttpException("Видеозаписи не найдены!");
        }

        if($listLength == 1){
            return $response['items'][0];
        }

        $items = [];

        foreach ($response['items'] as $item){
            $items[$item['id']] = $item;
        }

        return $items;
    }

    public function sendRequest($routeParams){
        $action = $routeParams['action'];
        unset($routeParams['action']);
        
        $routeParams['key'] = $this->myApiKey;

        $params = [];

        foreach($routeParams as $key => $value){
            $params[] = $key.'='.$value;
        }

        $request = new Curl();

        $request
            ->setOption(CURLINFO_CONTENT_TYPE, 'application/json')
            ->get($this->youtubeApiURL.$action.'?'.implode('&', $params));

        if($request->responseCode != 200){
            echo $request->responseCode;
            //var_dump($request->response);
        }

        return Json::decode($request->response);

        //return
    }

}