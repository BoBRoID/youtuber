<?php

use yii\db\Migration;

/**
 * Handles the dropping for table `links`.
 */
class m160514_192112_drop_links extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->dropColumn(\common\models\Video::tableName(), 'link');
        $this->dropColumn(\common\models\Link::tableName(), 'link');
        $this->dropColumn(\common\models\Video::tableName(), 'link_hash');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {

    }
}
