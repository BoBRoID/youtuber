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
/**
 * Created by PhpStorm.
 * User: bobroid
 * Date: 10.05.16
 * Time: 15:07
 * @var \frontend\models\YoutubeVideo $video
 */

echo /*\yii\bootstrap\Html::tag('h1', $video->name),
    \yii\bootstrap\Html::tag('div',
        \yii\bootstrap\Html::tag('span', 'Лайков: '.$video->likes, ['class' => '']).
        \yii\bootstrap\Html::tag('span', 'Дизлайков: '.$video->dislikes, ['class' => '']).
        \yii\bootstrap\Html::tag('span', 'Просмотров: '.$video->views, ['class' => ''])
    ),
    \yii\bootstrap\Html::tag('div', 'Загружено: '.$video->uploaded);*/'';

?>
<div class="row" style="margin-top: 20px">
    <div class="col-xs-10 col-lg-offset-1">
        <div class="col-xs-12">
            <h4><?=$video->name?></h4>
        </div>
        <div class="col-xs-12">
            <iframe width="100%" height="520" src="https://www.youtube.com/embed/<?=preg_replace('/(.*)\?v=/', '', $video->link)?>" frameborder="0" allowfullscreen></iframe>
        </div>
        <div class="col-xs-6">
            <div class="likes-dislikes">
                <span><?=FA::i('upload', ['title' => 'Загружено'])->size(FA::SIZE_2X)?>&nbsp;Загружено:&nbsp;<?=\Yii::$app->formatter->asDate($video->uploaded, 'php:d.m.Y')?>&nbsp;</span>
                <span><?=FA::i('eye', ['title' => 'Просмотров'])->size(FA::SIZE_2X)?>&nbsp;Просмотров:&nbsp;<?=number_format($video->views, 0, '.', ' ')?>&nbsp;</span>
                <span><?=FA::i('thumbs-o-up', ['title' => 'Лайков'])->size(FA::SIZE_2X)?>&nbsp;Понравилось:&nbsp;<?=number_format($video->likes, 0, '.', ' ')?>&nbsp;</span>
                <span><?=FA::i('thumbs-o-down', ['title' => 'Дизлайков'])->size(FA::SIZE_2X)?>&nbsp;Не понравилось:&nbsp;<?=number_format($video->dislikes, 0, '.', ' ')?>&nbsp;</span>
            </div>
        </div>
    </div>
</div>
