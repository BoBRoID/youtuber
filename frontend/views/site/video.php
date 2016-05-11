<?php
/**
 * Created by PhpStorm.
 * User: bobroid
 * Date: 10.05.16
 * Time: 15:07
 * @var \frontend\models\YoutubeVideo $video
 */

echo \yii\bootstrap\Html::tag('h1', $video->name),
    \yii\bootstrap\Html::tag('div',
        \yii\bootstrap\Html::tag('span', 'Лайков: '.$video->likes, ['class' => '']).
        \yii\bootstrap\Html::tag('span', 'Дизлайков: '.$video->dislikes, ['class' => '']).
        \yii\bootstrap\Html::tag('span', 'Просмотров: '.$video->views, ['class' => ''])
    ),
    \yii\bootstrap\Html::tag('div', 'Загружено: '.$video->uploaded);

?>
<div class="col-xs-12">
    <iframe width="1080" height="520" src="https://www.youtube.com/embed/<?=preg_replace('/(.*)\?v=/', '', $video->link)?>" frameborder="0" allowfullscreen></iframe>
</div>
