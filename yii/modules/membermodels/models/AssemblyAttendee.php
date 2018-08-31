<?php

namespace app\modules\membermodels\models;

/**
 * This is the model class for table "members.assembly_attendees".
 *
 * @property integer $assembly_id
 * @property integer $person_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Assembly $assembly
 * @property Person $person
 */
class AssemblyAttendee extends MemberDbRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'assembly_attendees';
    }
    
    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['assembly_id', 'person_id'], 'required'],
            [['assembly_id', 'person_id'], 'number']
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAssembly() {
        return $this->hasOne(Assembly::className(), ['id' => 'assembly_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerson() {
        return $this->hasOne(Person::className(), ['id' => 'person_id']);
    }

}
