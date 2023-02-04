<?php

use yii\db\Migration;

/**
 * Class m230202_211730_add_link
 */
class m230202_211730_add_link extends Migration
{
    const BOOK = '{{%book}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(self::BOOK, 'link', $this->string()->notNull()->defaultValue(""));
        $this->addColumn(self::BOOK, 'amazon_image', $this->string()->notNull()->defaultValue(""));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230202_211730_add_link cannot be reverted.\n";

        return false;
    }
}
