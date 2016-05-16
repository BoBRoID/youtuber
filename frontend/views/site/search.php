<?php

/** @var \common\models\Video $video */

use rmrevin\yii\fontawesome\FA;

\rmrevin\yii\fontawesome\cdn\AssetBundle::register($this);

$css = <<<'CSS'
.likes-dislikes span{
    display: block;
}

.likes-dislikes span, .likes-dislikes span i{
    line-height: 30px;
    vertical-align: bottom;
}
CSS;

$this->registerCss($css);

$this->title = 'Youtuber - поиск';
?>
<div class="row">
    <blockquote>
        <h1>Поиск</h1>
        <p>
            Поиск на нашем сайте позволяет вам найти видео по любому критерию! Ищите видео, и смотрите их подряд!
        </p>
    </blockquote>
    <?php
        \yii\widgets\Pjax::begin([
            'id'        =>  'rating-grid',
            'timeout'   =>  2000
        ]);
        echo \yii\grid\GridView::widget([
            'dataProvider'  =>  $dataProvider,
            'columns'       =>  [
                [
                    'class' =>  \yii\grid\SerialColumn::className()
                ],
                [
                    'format'    =>  'raw',
                    'attribute' =>  'name',
                    'value'     =>  function($model){
                        if(!empty($model->youtubeID)){
                            return \yii\bootstrap\Html::a($model->name, '/search-video/'.$model->youtubeID, ['data-pjax' => 0]);
                        }
                        return $model->name;
                    }
                ],
                [
                    'attribute' =>  'views',
                    'value'     =>  function($model){
                        return number_format($model->views, 0, '.', ' ');
                    }
                ],
                [
                    'attribute' =>  'likes',
                    'value'     =>  function($model){
                        return number_format($model->likes, 0, '.', ' ');
                    }
                ],
                [
                    'attribute' =>  'dislikes',
                    'value'     =>  function($model){
                        return number_format($model->dislikes, 0, '.', ' ');
                    }
                ],
                'uploaded',
                [
                    'attribute' =>  'checked',
                    'value'     =>  function($model){
                        //return $model->checked;
                        return \Yii::$app->formatter->asRelativeTime(strtotime($model->checked));
                    }
                ]
            ]
        ]);
        \yii\widgets\Pjax::end();
    ?>
</div>
