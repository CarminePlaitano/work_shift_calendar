<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%event_x_employee}}`.
 */
class m250521_132739_create_event_x_employee_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%event_x_employee}}', [
            'id' => $this->primaryKey(),
            'resource' => $this->integer()->notNull(),
            'event' => $this->integer()->notNull(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex(
            'idx-event_x_employee-resource',
            'event_x_employee',
            'resource'
        );

        $this->createIndex(
            'idx-event_x_employee-event',
            'event_x_employee',
            'event'
        );

        $this->addForeignKey(
            'fk-event_x_employee-resource',
            'event_x_employee',
            'resource',
            'employee',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-event_x_employee-event',
            'event_x_employee',
            'event',
            'event',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%event_x_employee}}');
    }
}
