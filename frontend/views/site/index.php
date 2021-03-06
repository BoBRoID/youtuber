<?php

/* @var $this yii\web\View */


$js = <<<'JS'
setInterval(function(){if($("#autoUpdateCheckbox").prop('checked')){$.pjax.reload({container: '#last-added', timeout: '5000'});}}, 2500);
JS;

$this->registerJs($js);

$this->registerMetaTag(['name' => 'advmaker-verification', 'content' => 'f60b0c2eb22a0cbeb01385b7a32c2a42']);
$this->registerMetaTag(['name' => 'keywords', 'content' => 'Рейтинг видео youtube, Youtuber, видео с youtube, видео, статистика youtube, топ видео youtube, топ youtube, топ ютуб, видео с ютуба, статистика ютуба'], 'keywords');
$this->registerMetaTag(['name' => 'description', 'content' => 'Статистика и рейтинг видео с youtube: поиск и сортировка по названию, лайкам, дизлайкам и прочему!'], 'description');

$css = <<<'CSS'
.banners small a{
color: transparent !important;
}
CSS;

$this->registerCss($css);

$this->title = 'Youtuber - статистика роликов с youtube';
?>
<div class="site-index">
    <blockquote>
        <h3>Youtuber - найди видео!</h3>
        <p>
            Посмотрите статистику видео на youtube: место по колличеству лайков, дизлайков, дате добавления и прочим параметрам.
            Для поиска видео воспользуйтесь поиском вверху (поиск по сайту), или вставьте ссылку на видео в поле ниже.
            Сервис отображает актуальные данные за последний час.
        </p>
    </blockquote>
    <div class="jumbotron col-xs-12" style="margin-bottom: 0;">
        <?php
        $model = new \frontend\models\FindVideoForm();

        $form = \yii\bootstrap\ActiveForm::begin([
            'id'        =>  'searchForm',
            'action'    =>  '/video'
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
    <div class="banners" style="margin-top: 20px;">
        <div class="col-xs-12">
            <!--noindex--><div id="ambn57023" style="margin: 0 auto"></div><!--/noindex-->
        </div>
        <div class="col-xs-12">
            <table style='width:708px;height:60px;display:block;border:0;margin:0 auto;padding:0;'>
                <tr><td valign='top'>
                <!-- Ukrainian Banner Network 120х60 START -->
                <center><script type='text/javascript'>
                var _ubn=_ubn||{sid:Math.round((Math.random()*10000000)),data:[]};
                (function(){var n=document.getElementsByTagName('script');
                _ubn.data.push({user: 110688, format_id: 4, page: 1,
                pid: Math.round((Math.random()*10000000)),placeholder: n[n.length-1]});
                if(!_ubn.code)(function() {var script = document.createElement('script');
                script.type = 'text/javascript'; _ubn.code= script.async = script.defer = true;
                script.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'banner.kiev.ua/j/banner.js?'+_ubn.sid;
                n[0].parentNode.insertBefore(script,n[0]);})();})();
                </script><br>
                <small><a href='https://www.bannerka.ua/' target=_top>Интернет реклама УБС</a></small></center>
                <!-- Ukrainian Banner Network 120х60 END -->
                </td>
                <td valign='top'>
                <!-- Ukrainian Banner Network 468x60 START -->
                <center><script type='text/javascript'>
                var _ubn=_ubn||{sid:Math.round((Math.random()*10000000)),data:[]};
                (function(){var n=document.getElementsByTagName('script');
                _ubn.data.push({user: 110688, format_id: 1, page: 1,
                pid: Math.round((Math.random()*10000000)),placeholder: n[n.length-1]});
                if(!_ubn.code)(function() {var script = document.createElement('script');
                script.type = 'text/javascript'; _ubn.code= script.async = script.defer = true;
                script.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'banner.kiev.ua/j/banner.js?'+_ubn.sid;
                n[0].parentNode.insertBefore(script,n[0]);})();})();
                </script><br>
                <small><a href='https://www.bannerka.ua/' target=_top>Интернет реклама УБС</a></small></center>
                <!-- Ukrainian Banner Network 468x60 END -->
                </td>
                <td valign='top'>
                <!-- Ukrainian Banner Network 120х60 START -->
                <center><script type='text/javascript'>
                var _ubn=_ubn||{sid:Math.round((Math.random()*10000000)),data:[]};
                (function(){var n=document.getElementsByTagName('script');
                _ubn.data.push({user: 110688, format_id: 4, page: 1,
                pid: Math.round((Math.random()*10000000)),placeholder: n[n.length-1]});
                if(!_ubn.code)(function() {var script = document.createElement('script');
                script.type = 'text/javascript'; _ubn.code= script.async = script.defer = true;
                script.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'banner.kiev.ua/j/banner.js?'+_ubn.sid;
                n[0].parentNode.insertBefore(script,n[0]);})();})();
                </script><br>
                <small><a href='https://www.bannerka.ua/' target=_top>Интернет реклама УБС</a></small></center>
                <!-- Ukrainian Banner Network 120х60 END -->
                </td></tr>
            </table>
        </div>
        <?php
        \yii\widgets\Pjax::begin([
            'id'            =>  'last-added',
            'timeout'       =>  10000
        ]);

        echo \yii\bootstrap\Html::tag('h4', "Уже ".\common\models\Video::find()->count()." видео на сайте!", ['style' => 'text-align: center; vertical-align: middle; line-height: 50px;']);

        echo \yii\bootstrap\Html::tag('h5', 'Последние обновлённые видео с более чем 500 000 просмотрами:', ['style' => 'margin-left: 15px']);
        echo \yii\bootstrap\Html::checkbox('', true, ['style' => 'margin-left: 15px;', 'label' => 'Обновлять автоматически', 'id' => 'autoUpdateCheckbox']);
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

                        if(!empty($model->youtubeID)){
                            return \yii\bootstrap\Html::a($name, '/video/'.$model->youtubeID, ['title' => $model->name, 'data-pjax' => 0]);
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
                        return \Yii::$app->formatter->asRelativeTime(strtotime($model->checked));
                    }
                ]
            ]
        ]);

        \yii\widgets\Pjax::end();
        ?>
    </div>
    <div class="clearfix"></div>
</div>
<!--noindex--><script type="text/javascript" src="//am15.net/bn.php?s=73455&f=6&d=57023" ></script><!--/noindex-->