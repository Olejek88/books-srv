<?php

use yii\db\Migration;

/**
 * Class m230223_202315_authors
 */
class m230223_202315_authors extends Migration
{
    const AUTHOR = '{{%author}}';
    const BOOK = '{{%book}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf16 COLLATE utf16_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(self::AUTHOR, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(36)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'description' => $this->text()->notNull(),
            'imageUrl' => $this->string(200)->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')
        ], $tableOptions);

        $this->renameColumn(self::BOOK, 'author', 'authorName');
        $this->addColumn(self::BOOK, 'authorUuid', $this->string(100));

        $this->createIndex(
            'idx-authorUuid',
            self::BOOK,
            'authorUuid'
        );

        $this->addForeignKey(
            'fk-book-authorUuid',
            self::BOOK,
            'authorUuid',
            self::AUTHOR,
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
        echo "m230223_202315_authors cannot be reverted.\n";
        return false;
    }
}
