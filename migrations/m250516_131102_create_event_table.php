<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%event}}`.
 */
class m250516_131102_create_event_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%event}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'start' => $this->dateTime()->notNull(),
            'end' => $this->dateTime()->notNull(),
            'type' => $this->integer()->notNull(),
            'color' => $this->string(7)->defaultValue('#779ECB'),
            'display' => $this->string(),
            'all_day' => $this->boolean()->defaultValue(false),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%event}}');
    }
}