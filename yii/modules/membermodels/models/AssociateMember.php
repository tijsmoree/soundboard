<?php

namespace app\modules\membermodels\models;

/**
 * This is the model class for table "members.associate_members".
 *
 * @property integer $id
 * @property integer $person_id
 * @property string $registration
 * @property string $deregistration
 * @property string $expiration
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 *
 * @property array $viewAttributes
 * @property string $period
 *
 * @property Person $person
 */
class AssociateMember extends MemberDbRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'associate_members';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['person_id', 'expiration'], 'required'],
            [['description'], 'string'],
            [['registration', 'deregistration', 'expiration'], 'date', 'format' => 'yyyy-mm-dd'],
            [['person_id'], 'number']
        ];
    }

    public function beforeSave($insert) {
        $this->description = $this->description ?? "";

        return parent::beforeSave($insert);
    } 

    public function getPeriod() {
        if ($this->registration == null) {
            $start = 'Onbekend';
        } else {
            $registration = strtotime($this->registration);
            if (strpos($this->registration, '-01-01') !== false) {
                $start = date('Y', $registration);
            } elseif (strpos($this->registration, '0000-00-00') !== false) {
                $start = 'Onbekend';
            } else {
                $start = date('d M Y', $registration);
            }
        }

        if ($this->deregistration == null) {
            $end = 'Heden';
        } else {
            $deregistration = strtotime($this->deregistration);
            if (strpos($this->deregistration, '-01-01') !== false) {
                $end = date('Y', $deregistration);
            } elseif (strpos($this->deregistration, '0000-00-00') !== false) {
                $end = 'Onbekend';
            } else {
                $end = date('d M Y', $deregistration);
            }
        }

        if ($start == $end) {
            $period = $start;
        } else {
            $period = $start . ' - ' . $end;
        }

        return $period;
    }

    public function getViewAttributes() {
        return $this->getAttributes([
            'id', 'person_id', 'registration', 'deregistration', 'expiration', 'description'
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerson() {
        return $this->hasOne(Person::className(), ['id' => 'person_id']);
    }

}
