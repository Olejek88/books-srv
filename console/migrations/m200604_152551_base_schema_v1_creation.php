<?php

use yii\db\Migration;

/**
 * Class m230107_152551_base_schema_v1_creation
 */
class m200604_152551_base_schema_v1_creation extends Migration
{
    const BOOK = '{{%book}}';
    const CATEGORY = '{{%category}}';
    const USER = '{{%user}}';

    public function safeUp()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf16 COLLATE utf16_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(self::CATEGORY, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(36)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')
        ], $tableOptions);

        $this->createTable(self::BOOK, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(36)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'author' => $this->string()->notNull(),
            'description' => $this->text()->notNull(),
            'categoryUuid' => $this->string(100)->notNull(),
            'imageUrl' => $this->string(200)->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')
        ], $tableOptions);

        $this->createIndex(
            'idx-categoryUuid',
            self::BOOK,
            'categoryUuid'
        );

        $this->addForeignKey(
            'fk-category-categoryUuid',
            self::BOOK,
            'categoryUuid',
            self::CATEGORY,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(self::BOOK);
        $this->dropTable(self::CATEGORY);
        return true;
    }
}
