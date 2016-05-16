<?php

/** @var \common\models\Video $video */

use kartik\form\ActiveForm;
use rmrevin\yii\fontawesome\FA;
use yii\bootstrap\Html;

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
    <div class="col-xs-9">
        <?php
        \yii\widgets\Pjax::begin([
            'id'        =>  'rating-grid',
            'timeout'   =>  2000
        ]);
        echo \yii\grid\GridView::widget([
            'dataProvider'  =>  $dataProvider,
            'options'       =>  [
                'style'     =>  'width: 100%'
            ],
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
                [
                    'label'     =>  'Загружено',
                    'attribute' =>  'uploaded'
                ]
            ]
        ]);
        \yii\widgets\Pjax::end();
        ?>
    </div>
    <div class="col-xs-3" style="margin-top: 23px">
        <?php $form = ActiveForm::begin([
            'type' => ActiveForm::TYPE_VERTICAL,
            'method'    =>  'get'
        ])?>
        <?=$form->field($searchModel, 'name'),
        $form->field($searchModel, 'views', [
            'addon' =>  ['prepend' => ['content' => $form->field($searchModel, 'viewsSearchType', ['inputOptions' => ['style' => 'width: 30px;'], 'options' => ['class' => 'col-xs-2', 'style' => 'margin: -7px 0px 0px -32px;']])->dropDownList($searchModel->operands)->label(false)->error(false)]]
        ]),
        $form->field($searchModel, 'likes', [
            'addon' =>  ['prepend' => ['content' => $form->field($searchModel, 'likesSearchType', ['inputOptions' => ['style' => 'width: 30px;'], 'options' => ['class' => 'col-xs-2', 'style' => 'margin: -7px 0px 0px -32px;']])->dropDownList($searchModel->operands)->label(false)->error(false)]]
        ]),
        $form->field($searchModel, 'dislikes', [
            'addon' =>  ['prepend' => ['content' => $form->field($searchModel, 'dislikesSearchType', ['inputOptions' => ['style' => 'width: 30px;'], 'options' => ['class' => 'col-xs-2', 'style' => 'margin: -7px 0px 0px -32px;']])->dropDownList($searchModel->operands)->label(false)->error(false)]]
        ]),
        $form->field($searchModel, 'uploaded', [
            'addon' =>  ['prepend' => ['content' => $form->field($searchModel, 'uploadedSearchType', ['inputOptions' => ['style' => 'width: 30px;'], 'options' => ['class' => 'col-xs-2', 'style' => 'margin: -7px 0px 0px -32px;']])->dropDownList($searchModel->operands)->label(false)->error(false)]]
        ]),
        Html::button('Искать!', ['class' => 'btn btn-success btn-block', 'type' => 'submit'])?>
        <?php $form->end()?>
    </div>
</div>
