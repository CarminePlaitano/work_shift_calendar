<?php

namespace backend\modules\work_shift_calendar\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class EventXEmployee extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%event_x_employee}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['resource', 'event'], 'required'],
            [['resource', 'event'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['resource'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['resource' => 'id']],
            [['event'], 'exist', 'skipOnError' => true, 'targetClass' => Event::class, 'targetAttribute' => ['event' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'resource' => 'Employee ID',
            'event' => 'Event ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Employee]].
     * @return ActiveQuery
     */
    public function getEmployee(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'resource']);
    }

    /**
     * Gets query for [[EventRel]].
     * @return ActiveQuery
     */
    public function getEvent(): ActiveQuery
    {
        return $this->hasOne(Event::class, ['id' => 'event']);
    }
}