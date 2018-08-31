<?php

namespace app\modules\membermodels\models;

/**
 * This is the model class for table "members.faculty_employments".
 *
 * @property integer $id
 * @property integer $person_id
 * @property integer $faculty_department_id
 * @property string $installation
 * @property string $discharge
 * @property string $function
 * @property string $created_at
 * @property string $updated_at
 *
 * @property FacultyDepartment $facultyDepartment
 * @property Person $person
 */
class FacultyEmployment extends MemberDbRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'faculty_employments';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['person_id', 'faculty_department_id'], 'required'],
            [['function'], 'string'],
            [['installation', 'discharge'], 'date', 'format' => 'yyyy-mm-dd']
        ];
    }

    /**
     * @return array
     */
    public function getDepartmentViewAttributes() {
        $result = $this->getAttributes([
            'id',
            'person_id',
            'installation',
            'discharge',
            'period',
            'active',
            'function'
        ]);
        $result['person'] = $this->person->getAttributes([
            'name'
        ]);
        return $result;
    }

    public function getActive() {
        return ($this->discharge == null);
    }

    public function getPeriod() {
        $period = static::dateToString($this->installation);

        if ($this->discharge !== null) {
            $period .= ' - ' . static::dateToString($this->discharge);
        }

        return $period;
    }

    /**
     * @return array
     */
    public function getViewAttributes() {
        $result = $this->getAttributes([
            'id',
            'person_id',
            'faculty_department_id',
            'installation',
            'discharge',
            'period',
            'function'
        ]);
        $result['person'] = $this->person->getAttributes([
            'name',
            'id'
        ]);
        $result['facultyDepartment'] = $this->facultyDepartment->getAttributes([
            'name',
            'id'
        ]);
        return $result;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFacultyDepartment() {
        return $this->hasOne(FacultyDepartment::className(), ['id' => 'faculty_department_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerson() {
        return $this->hasOne(Person::className(), ['id' => 'person_id']);
    }

    public static function dateToString($date) {
        if ($date == '0000-00-00' || $date == null) {
            $string = 'Onbekend';
        } elseif (strpos($date, '-01-01') !== false) {
            $string = substr($date, 0, 4);
        } else {
            $string = date('d M y', strtotime($date));
        }

        return $string;
    }
}
