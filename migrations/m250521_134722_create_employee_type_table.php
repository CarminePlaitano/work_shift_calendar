<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%employee_type}}`.
 */
class m250521_134722_create_employee_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%employee_type}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'label' => $this->string()->notNull(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex(
            'idx-employee-type',
            'employee',
            'type'
        );

        $this->addForeignKey(
            'fk-employee-type',
            'employee',
            'type',
            'employee_type',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%employee_type}}');
    }
}
