<?php

use yii\db\Migration;

class m160510_132709_video_link_hash extends Migration
{
    public function up()
    {
        $this->addColumn(\common\models\Video::tableName(), 'link_hash', \yii\db\Schema::TYPE_STRING);
    }

    public function down()
    {
        $this->dropColumn(\common\models\Video::tableName(), 'link_hash');

        return false;
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
