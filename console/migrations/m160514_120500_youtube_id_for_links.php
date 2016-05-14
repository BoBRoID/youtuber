<?php

use yii\db\Migration;

class m160514_120500_youtube_id_for_links extends Migration
{
    public function up()
    {
        $this->addColumn(\common\models\Link::tableName(), 'youtubeID', \yii\db\Schema::TYPE_STRING.'(11) NOT NULL');

        $this->createIndex('youtubeID', \common\models\Link::tableName(), 'youtubeID');
    }

    public function down()
    {
        $this->dropColumn(\common\models\Link::tableName(), 'youtubeID');

        return true;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
