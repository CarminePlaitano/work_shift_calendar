<?php

namespace carmineplaitano\work_shift_calendar\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "event".
 *
 * @property int $id
 * @property string $title
 * @property string $start
 * @property string $end
 * @property int $type
 * @property string|null $color
 * @property string|null $display
 * @property bool|null $all_day
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class Event extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'event';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['title', 'start', 'end', 'type'], 'required'],
            [['start', 'end', 'created_at', 'updated_at'], 'safe'],
            [['type'], 'integer'],
            [['all_day'], 'boolean'],
            [['title', 'display'], 'string', 'max' => 255],
            [['color'], 'string', 'max' => 7],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'start' => 'Start Time',
            'end' => 'End Time',
            'type' => 'Resource ID',
            'color' => 'Color',
            'display' => 'Display',
            'all_day' => 'All Day',
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
     * Relazione verso Employee tramite la pivot event_x_employee
     * @return ActiveQuery
     */
    public function getEmployees(): ActiveQuery
    {
        return $this->hasMany(Employee::class, ['id' => 'resource'])->via('eventXEmployees');
    }
}