<?php

use yii\db\Migration;

class m160512_182731_additional_info_for_video extends Migration
{
    public function up()
    {
        $this->addColumn(\common\models\Video::tableName(), 'thumbnail', \yii\db\Schema::TYPE_STRING);
        $this->addColumn(\common\models\Video::tableName(), 'added', \yii\db\Schema::TYPE_DATETIME);
    }

    public function down()
    {
        $this->dropColumn(\common\models\Video::tableName(), 'thumbnail');
        $this->dropColumn(\common\models\Video::tableName(), 'added');

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
