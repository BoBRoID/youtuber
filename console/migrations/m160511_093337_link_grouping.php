<?php

use yii\db\Migration;

class m160511_093337_link_grouping extends Migration
{
    public function up()
    {
        $this->addColumn(\common\models\Link::tableName(), 'group', \yii\db\Schema::TYPE_INTEGER.' UNSIGNED NOT NULL DEFAULT 0');

        $this->createTable('workers', [
            'id'        =>  \yii\db\Schema::TYPE_INTEGER.' UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT',
            'groupID'   =>  \yii\db\Schema::TYPE_INTEGER.' UNSIGNED NOT NULL DEFAULT 0'
        ]);
    }

    public function down()
    {
        $this->dropTable('workers');
        return $this->dropColumn(\common\models\Link::tableName(), 'group');
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
