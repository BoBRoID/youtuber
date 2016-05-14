<?php

use yii\db\Migration;

class m160509_141035_youtuber_init extends Migration
{
    public function up()
    {
        $this->createTable('videos', [
            'link'      =>  \yii\db\Schema::TYPE_STRING,
            'name'      =>  \yii\db\Schema::TYPE_STRING,
            'views'     =>  \yii\db\Schema::TYPE_BIGINT.' UNSIGNED NOT NULL DEFAULT 0',
            'likes'     =>  \yii\db\Schema::TYPE_BIGINT.' UNSIGNED NOT NULL DEFAULT 0',
            'dislikes'  =>  \yii\db\Schema::TYPE_BIGINT.' UNSIGNED NOT NULL DEFAULT 0',
            'uploaded'  =>  \yii\db\Schema::TYPE_STRING,
            'checked'   =>  \yii\db\Schema::TYPE_TIMESTAMP
        ]);

        $this->createTable('links', [
            'added'     =>  \yii\db\Schema::TYPE_TIMESTAMP,
            'link'      =>  \yii\db\Schema::TYPE_STRING
        ]);

        $this->createIndex('link', \common\models\Link::tableName(), 'link');
    }

    public function down()
    {
        $this->dropTable('videos');

        $this->dropIndex('link', \common\models\Link::tableName());

        return $this->dropTable('links');
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
