<?php
namespace sergmoro1\comment\migrations;

use yii\db\Schema;
use yii\db\Migration;

/**
 * @author Sergey Morozov <sergey@vorst.ru>
 */
class m180206_175933_create_comment extends Migration
{
    private const TABLE_COMMENT = '{{%comment}}';
    private const TABLE_USER    = '{{%user}}';

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(static::TABLE_COMMENT, [
            'id'        => $this->primaryKey(),
            'model'     => $this->integer()->notNull(),
            'parent_id' => $this->integer()->notNull(),
            'user_id'   => $this->integer()->notNull(),
            'content'   => $this->text(),
            'status'    => $this->integer()->defaultValue(1),
            'thread'    => $this->string(32)->defaultValue(time() + rand(1000, 9999)),
            'last'      => $this->boolean()->defaultValue(1),

            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ], $tableOptions);

        $this->createIndex('idx-model-parent-status', static::TABLE_COMMENT, ['model', 'parent_id', 'status']);
        $this->createIndex('idx-status-thread', static::TABLE_COMMENT, ['status', 'thread']);
        $this->addForeignKey ('fk-comment-user', static::TABLE_COMMENT, 'user_id', static::TABLE_USER, 'id', 'CASCADE');

        $this->addCommentOnColumn(static::TABLE_COMMENT, 'model',     'Model code');
        $this->addCommentOnColumn(static::TABLE_COMMENT, 'parent_id', 'Parent ID in a model');
        $this->addCommentOnColumn(static::TABLE_COMMENT, 'user_id',   'User ID who left the comment');
        $this->addCommentOnColumn(static::TABLE_COMMENT, 'content',   'Content');
        $this->addCommentOnColumn(static::TABLE_COMMENT, 'status',    'Status');
        $this->addCommentOnColumn(static::TABLE_COMMENT, 'thread',    'Unique conversation code');
        $this->addCommentOnColumn(static::TABLE_COMMENT, 'last',      'Is that the last reply in the conversation?');
    }

    public function safeDown()
    {
        $this->dropTable(static::TABLE_COMMENT);
    }
}
