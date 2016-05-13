<?php

/* @var $this yii\web\View */


$js = <<<'JS'
setInterval(function(){$.pjax.reload({container: '#last-added'});}, 1500);
JS;

$this->registerJs($js);


$this->title = 'Youtuber - статистика роликов с youtube';
?>
<div class="site-index">
    <blockquote>
        <h3>Youtuber - найди видео!</h3>
        <p>
            Сервис создан для создания некой статистики видео на youtube: худшие, лучшие, по колличеству лайков\дизлайков.
            Всё просто: в поле ниже вставляем ссылку с youtube, и нажимаем искать. Затем мы перейдём на страницу видео,
            соберём данные о нём, и добавим видео в статистику.
            Видео индексируется и добавляется в базу данных. Если кинуть ссылку на видео с www, то даже если такое видео есть в базе данных, оно проиндексируется сразу же.
        </p>
    </blockquote>
    <div class="jumbotron col-xs-12" style="margin-bottom: 0;">
        <?php
        $model = new \frontend\models\FindVideoForm();

        $form = \yii\bootstrap\ActiveForm::begin([
            'id'        =>  'searchForm',
            'action'    =>  '/search-video'
        ]);

        echo $form->field($model, 'url', [
            'labelOptions'  =>  [
                'style' =>  'font-size: 32px'
            ]
        ]),
            \yii\bootstrap\Html::button('Искать', ['type' => 'submit', 'class' => 'btn btn-success']);

        $form->end();
            ?>
    </div>
    <div class="" style="margin-top: 20px;">
        <?php
        \yii\widgets\Pjax::begin([
            'id'            =>  'last-added',
            'timeout'       =>  10000
        ]);

        echo \yii\bootstrap\Html::tag('h4', "Уже ".\common\models\Video::find()->count()." видео на сайте!", ['style' => 'text-align: center; vertical-align: middle; line-height: 50px;']);

        echo \yii\bootstrap\Html::tag('h5', 'Последние обновлённые видео с более чем 500 000 просмотрами:', ['style' => 'margin-left: 15px']);
        echo \yii\grid\GridView::widget([
            'dataProvider'  =>  $lastAddedProvider,
            'summary'       =>  false,
            'options'       =>  [
                'class' =>  'col-xs-12',
            ],
            'columns'       =>  [
                [
                    'class' =>  \yii\grid\SerialColumn::className()
                ],
                [
                    'format'    =>  'raw',
                    'attribute' =>  'name',
                    'value'     =>  function($model){
                        $name = mb_strlen($model->name) > 55 ? mb_substr($model->name, 0, 56).'...' : $model->name;

                        if(!empty($model->link_hash)){
                            return \yii\bootstrap\Html::a($name, '/search-video/'.$model->link_hash, ['title' => $model->name]);
                        }
                        return $name;
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
                [
                    'attribute' =>  'uploaded',
                    'encodeLabel'=> false,
                    'label'     =>  'Загружено<br>на Youtube'
                ],
                [
                    'attribute' =>  'checked',
                    'value'     =>  function($model){
                        return \Yii::$app->formatter->asRelativeTime(strtotime($model->checked) - (60 * 60 * 3));
                    }
                ]
            ]
        ]);

        \yii\widgets\Pjax::end();
        ?>
    </div>
    <div class="clearfix"></div>
</div>
