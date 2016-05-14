<?php

use yii\db\Migration;

class m160513_134032_optimize_links extends Migration
{
    public function up()
    {
        $this->addColumn(\common\models\Link::tableName(), 'index', \yii\db\Schema::TYPE_BIGPK);
        //$this->createIndex('link', \common\models\Link::tableName(), 'link', true);
    }

    public function down()
    {
        $this->dropColumn(\common\models\Link::tableName(), 'index');
        //$this->dropIndex('link', \common\models\Link::tableName());

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
