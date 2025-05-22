<?php

namespace backend\modules\work_shift_calendar\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%employee}}".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property int $type
 * @property string $created_at
 * @property string $updated_at
 *
 * @property EventXEmployee[] $eventXEmployees
 * @property Event[] $events
 */
class Employee extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%employee}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['first_name', 'last_name', 'type'], 'required'],
            [['type'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['first_name', 'last_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'type' => 'Type',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getEventXEmployees(): ActiveQuery
    {
        return $this->hasMany(EventXEmployee::class, ['event' => 'id']);
    }

    /**
     * Gets query for [[Events]] via pivot.
     * @return ActiveQuery
     */
    public function getEvents(): ActiveQuery
    {
        return $this->hasMany(Event::class, ['id' => 'event'])->via('eventXEmployees');
    }
}