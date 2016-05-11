<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">
    <div class="jumbotron">
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
</div>
