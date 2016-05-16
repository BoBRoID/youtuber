<?php

use yii\db\Migration;

class m160516_123055_index_search_fields extends Migration
{
    public function up()
    {
        $this->createIndex('views', \common\models\Video::tableName(), 'views');
        $this->createIndex('likes', \common\models\Video::tableName(), 'likes');
        $this->createIndex('dislikes', \common\models\Video::tableName(), 'dislikes');
        $this->createIndex('uploaded', \common\models\Video::tableName(), 'uploaded');
        $this->createIndex('categoryID', \common\models\Video::tableName(), 'categoryID');
    }

    public function down()
    {
        echo "m160516_123055_index_search_fields cannot be reverted.\n";
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
