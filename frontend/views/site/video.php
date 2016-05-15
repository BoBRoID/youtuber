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

$this->title = $video->name.' - Youtuber';
?>
<div class="row" style="margin-top: 20px">

    <div class="col-xs-12">
        <h4><?=$video->name?></h4>
    </div>
    <div class="row col-xs-12" style="height: 530px;">
        <?=\yii\bootstrap\Html::a(
            FA::i('arrow-left', ['style' => 'line-height: 530px; text-align: right; width: 100%'])->size(FA::SIZE_4X),
            empty($video->previous) ? '#' : \yii\helpers\Url::to(['/search-video/'.$video->previous->youtubeID]),
            [
                'class' =>  'col-md-1 col-lg-1 hidden-xs hidden-sm btn-link',
                'title' =>  empty($video->previous) ? 'Нет предыдущего видео' : 'Предыдущее видео',
                'style' =>  'height: 100%',
                (empty($video->previous) ? 'disabled' : 'enabled')   =>  'disabled'
            ]
        )?>
        <div class="col-lg-10 col-md-10 col-xs-12">
            <iframe width="100%" height="520" src="https://www.youtube.com/embed/<?=$video->youtubeID?>" frameborder="0" allowfullscreen></iframe>
        </div>
        <?=\yii\bootstrap\Html::a(
            FA::i('arrow-right', ['style' => 'line-height: 530px; text-align: left; width: 100%'])->size(FA::SIZE_4X),
            empty($video->next) ? '#' : \yii\helpers\Url::to(['/search-video/'.$video->next->youtubeID]),
            [
                'class' =>  'col-md-1 col-lg-1 hidden-xs hidden-sm btn-link',
                'style' =>  'height: 100%',
                'title' =>  empty($video->next) ? 'Нет следующего видео' : 'Следующее видео',
                (empty($video->next) ? 'disabled' : 'enabled')   =>  'disabled'
            ]
        )?>
    </div>
    <div class="col-xs-6">
        <div class="likes-dislikes">
            <?php if(strtotime($video->uploaded)){ ?>
            <span><?=FA::i('upload', ['title' => 'Загружено'])->size(FA::SIZE_2X)?>&nbsp;Загружено:&nbsp;<?=\Yii::$app->formatter->asDate($video->uploaded, 'php:d.m.Y')?>&nbsp;</span>
            <?php } ?>
            <span><?=FA::i('eye', ['title' => 'Просмотров'])->size(FA::SIZE_2X)?>&nbsp;Просмотров:&nbsp;<?=number_format($video->views, 0, '.', ' ')?>&nbsp;</span>
            <span><?=FA::i('thumbs-o-up', ['title' => 'Лайков'])->size(FA::SIZE_2X)?>&nbsp;Понравилось:&nbsp;<?=number_format($video->likes, 0, '.', ' ')?>&nbsp;</span>
            <span><?=FA::i('thumbs-o-down', ['title' => 'Дизлайков'])->size(FA::SIZE_2X)?>&nbsp;Не понравилось:&nbsp;<?=number_format($video->dislikes, 0, '.', ' ')?>&nbsp;</span>
        </div>
    </div>
</div>
