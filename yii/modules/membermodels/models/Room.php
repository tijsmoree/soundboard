<?php

namespace app\modules\membermodels\models;

/**
 * This is the model class for table "members.rooms".
 *
 * @property integer $id
 * @property string $name
 * @property string $code
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 *
 * @property RoomAccess[] $roomAccesses
 * @property Person[] $people
 */
class Room extends MemberDbRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'rooms';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['name', 'code'], 'required'],
            [['name', 'code'], 'string']
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoomAccesses() {
        return $this->hasMany(RoomAccess::className(), ['room_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeople() {
        return $this->hasMany(Person::className(), ['id' => 'person_id'])->viaTable('room_access', ['room_id' => 'id']);
    }

    public function delete() {
        foreach ($this->roomAccesses as $link) {
            $link->delete();
        }

        return parent::delete();
    }
}
