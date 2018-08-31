<?php

namespace app\modules\membermodels\models;

/**
 * This is the model class for table "members.faculty_employees".
 *
 * @property integer $person_id
 * @property string $room
 * @property string $tu_phone
 * @property string $created_at
 * @property string $updated_at
 *
 * @property array $viewAttributes
 * @property FacultyEmployment[] $facultyEmployeeDepartments
 * @property FacultyDepartment[] $facultyDepartments
 * @property Person $person
 */
class FacultyEmployee extends MemberDbRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'faculty_employees';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['person_id'], 'required'],
            [['room', 'tu_phone'], 'string']
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFacultyEmployeeDepartments() {
        return $this->hasMany(FacultyDepartmentEmployee::className(), ['person_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFacultyDepartments() {
        return $this->hasMany(FacultyDepartment::className(), ['id' => 'faculty_departments_id'])->viaTable('faculty_department_employee', ['person_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerson() {
        return $this->hasOne(Person::className(), ['id' => 'person_id']);
    }

    /**
     * @return array
     */
    public function getViewAttributes() {
        $result = $this->getAttributes([
            'person_id',
            'room',
            'tu_phone'
        ]);

        return $result;
    }
}
