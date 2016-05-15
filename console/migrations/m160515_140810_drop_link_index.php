<?php

use yii\db\Migration;

/**
 * Handles the dropping for table `link_index`.
 */
class m160515_140810_drop_link_index extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->dropColumn(\common\models\Link::tableName(), 'index');
        //$this->dropPrimaryKey('index', \common\models\Link::tableName());
        //$this->addPrimaryKey('PRIMARY', \common\models\Link::tableName(), 'youtubeID');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {

    }
}
