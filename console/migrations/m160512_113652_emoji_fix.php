<?php

use yii\db\Migration;

class m160512_113652_emoji_fix extends Migration
{
    public function up()
    {
        $this->execute("ALTER DATABASE `youtuber` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
        $this->execute("ALTER TABLE `videos` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $this->execute("ALTER TABLE `videos` CHANGE `name` `name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT  ''");
    }

    public function down()
    {
        echo "m160512_113652_emoji_fix cannot be reverted.\n";

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
