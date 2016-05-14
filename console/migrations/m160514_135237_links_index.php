<?php

use yii\db\Migration;

class m160514_135237_links_index extends Migration
{
    public function up()
    {
        $this->createIndex('added', \common\models\Link::tableName(), 'added');
        $this->createIndex('added', \common\models\Video::tableName(), 'added');

        $this->execute("DELETE FROM `video` GROUP BY `youtubeID` HAVING COUNT(`youtubeID`) >= 2");

        $this->createIndex('youtubeID', \common\models\Video::tableName(), 'youtubeID', true);
    }

    public function down()
    {
        $this->dropIndex('added', \common\models\Link::tableName());
        $this->dropIndex('added', \common\models\Video::tableName());
        $this->dropIndex('youtubeID', \common\models\Video::tableName());

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
