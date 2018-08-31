<?php

namespace app\modules\membermodels\models;

/**
 * This is the model class for table "members.room_access".
 *
 * @property integer $person_id
 * @property integer $room_id
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Person $person
 * @property Room $room
 */
class RoomAccess extends MemberDbRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'room_access';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerson() {
        return $this->hasOne(Person::className(), ['id' => 'person_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoom() {
        return $this->hasOne(Room::className(), ['id' => 'room_id']);
    }

}
