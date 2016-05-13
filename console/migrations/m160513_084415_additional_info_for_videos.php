<?php

use yii\db\Migration;

class m160513_084415_additional_info_for_videos extends Migration
{
    public function up()
    {
        $this->createTable('thumbnails', [
            'videoID'   =>  \yii\db\Schema::TYPE_BIGINT.' UNSIGNED NOT NULL',
            'size'      =>  \yii\db\Schema::TYPE_INTEGER.' UNSIGNED NOT NULL DEFAULT 0',
            'link'      =>  \yii\db\Schema::TYPE_STRING
        ]);

        $this->dropColumn(\common\models\Video::tableName(), 'thumbnail');

        $this->addColumn(\common\models\Video::tableName(), 'id', \yii\db\Schema::TYPE_BIGINT.' UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE KEY');
        $this->addColumn(\common\models\Video::tableName(), 'youtubeID', \yii\db\Schema::TYPE_STRING.'(11)');
        $this->addColumn(\common\models\Video::tableName(), 'channelID', \yii\db\Schema::TYPE_STRING.'(24)');
        $this->addColumn(\common\models\Video::tableName(), 'categoryID', \yii\db\Schema::TYPE_INTEGER.' UNSIGNED NOT NULL DEFAULT 0');
        $this->addColumn(\common\models\Video::tableName(), 'liveBroadcast', \yii\db\Schema::TYPE_SMALLINT.'(1) UNSIGNED NOT NULL DEFAULT 0');
    }

    public function down()
    {
        $this->dropTable('thumbnails');

        $this->addColumn(\common\models\Video::tableName(), 'thumbnail', \yii\db\Schema::TYPE_STRING);

        $this->dropColumn(\common\models\Video::tableName(), 'id');
        $this->dropColumn(\common\models\Video::tableName(), 'youtubeID');
        $this->dropColumn(\common\models\Video::tableName(), 'channelID');
        $this->dropColumn(\common\models\Video::tableName(), 'categoryID');
        $this->dropColumn(\common\models\Video::tableName(), 'liveBroadcast');

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
