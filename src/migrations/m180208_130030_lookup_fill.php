<?php
namespace sergmoro1\comment\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m180208_130030_lookup_fill extends Migration
{
    private const LOOKUP   = '{{%lookup}}';
    private const PROPERTY = '{{%property}}';
    // Properties
    const COMMENT_STATUS = 4;
    
    public function up()
    {
        $this->insert(static::PROPERTY, ['id' => self::COMMENT_STATUS, 'name' => 'CommentStatus']);
        $this->insert(static::LOOKUP, ['name' => 'Ожидание', 'code' => 1, 'property_id' => self::COMMENT_STATUS, 'position' => 1]);
        $this->insert(static::LOOKUP, ['name' => 'Подтверждено', 'code' => 2, 'property_id' => self::COMMENT_STATUS, 'position' => 2]);
        $this->insert(static::LOOKUP, ['name' => 'Архив', 'code' => 3, 'property_id' => self::COMMENT_STATUS, 'position' => 3]);
    }

    public function down()
    {
        $this->delete(static::LOOKUP,   'property_id=' . self::COMMENT_STATUS);
        $this->delete(static::PROPERTY, 'id=' . self::COMMENT_STATUS);
    }
}
