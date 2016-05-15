<?php

use yii\db\Migration;

/**
 * Handles the dropping for table `bad_keys`.
 */
class m160514_155429_drop_bad_keys extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->dropIndex('link', \common\models\Link::tableName());
        //$this->dropPrimaryKey('index', \common\models\Link::tableName());
        //$this->addPrimaryKey('youtubeID', \common\models\Link::tableName(), 'youtubeID');
        //$this->createIndex('youtubeID', \common\models\Link::tableName(), 'youtubeID', true);
        $this->createIndex('checked', \common\models\Video::tableName(), 'checked');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {

    }
}
