<?php

namespace app\modules\membermodels\models;

/**
 * This is the model class for table "members.faculty_departments".
 *
 * @property integer $id
 * @property string $name
 * @property integer $is_support
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 *
 * @property FacultyEmployment[] $facultyEmployments
 * @property FacultyEmployee[] $facultyEmployees
 */
class FacultyDepartment extends MemberDbRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'faculty_departments';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['name'], 'required'],
            ['is_support', 'boolean'],
            [['name', 'description'], 'string']
        ];
    }

    public function beforeSave($insert) {
        $this->description = $this->description ?? "";

        return parent::beforeSave($insert);
    } 

    /**
     * @return array
     */
    public function getListAttributes() {
        return $this->getAttributes([
            'id',
            'name',
            'is_support',
            'description',
            'activeFacultyEmployments',
            'hasActiveFacultyEmployments',
            'lastInstallation'
        ]);
    }

    /**
     * @return array
     */
    public function getViewAttributes() {
        $result = $this->getAttributes([
            'id',
            'name',
            'description',
            'is_support'
        ]);
        $result['facultyEmployments'] = array_map(function ($i) {
            return $i->departmentViewAttributes;
        }, $this->facultyEmployments);

        return $result;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActiveFacultyEmployments() {
        return $this->hasMany(FacultyEmployment::className(), ['faculty_department_id' => 'id'])
            ->where('discharge IS NULL');
    }

    /**
     * @return boolean
     */
    public function getHasActiveFacultyEmployments() {
        return (count($this->activeFacultyEmployments) > 0);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastInstallation() {
        if (count($this->facultyEmployments) == 0) {
            return null;
        }

        $lastInstallation = null;
        foreach ($this->facultyEmployments as $employment) {
            $installation = strtotime($employment->installation);
            if ($lastInstallation == null || $installation > $lastInstallation) {
                $lastInstallation = $installation;
            }
        }
        return $lastInstallation;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFacultyEmployments() {
        return $this->hasMany(FacultyEmployment::className(), ['faculty_department_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFacultyEmployees() {
        return $this->hasMany(FacultyEmployee::className(), ['person_id' => 'person_id'])->viaTable('faculty_employments', ['faculty_department_id' => 'id']);
    }

}
