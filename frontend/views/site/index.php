<?php

/* @var $this yii\web\View */


$js = <<<'JS'
setInterval(function(){$.pjax.reload({container: '#last-added'});}, 1500);
JS;

$this->registerJs($js);


$this->title = 'My Yii Application';
?>
<div class="site-index">
    <div class="jumbotron col-xs-12">
        <?php
        $model = new \frontend\models\FindVideoForm();

        $form = \yii\bootstrap\ActiveForm::begin([
            'id'        =>  'searchForm',
            'action'    =>  '/search-video'
        ]);

        echo $form->field($model, 'url'),
            \yii\bootstrap\Html::button('Искать', ['type' => 'submit']);

        $form->end();
            ?>
    </div>
    <div class="jumbotron">
        <?php
        \yii\widgets\Pjax::begin([
            'id' => 'last-added'
        ]);

        echo \yii\bootstrap\Html::tag('span', "Всего видео на сайте: ".\common\models\Video::find()->count());

        echo \yii\grid\GridView::widget([
            'dataProvider'  =>  $lastAddedProvider,
            'summary'       =>  false,
            'options'       =>  [
                'class' =>  'col-xs-12'
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
                'uploaded',
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
</div>
