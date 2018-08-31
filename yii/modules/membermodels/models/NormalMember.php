<?php

namespace app\modules\membermodels\models;

/**
 * This is the model class for table "members.normal_members".
 *
 * @property integer $id
 * @property integer $person_id
 * @property integer $type_id
 * @property string $registration
 * @property string $deregistration
 * @property string $created_at
 * @property string $updated_at
 *
 * @property array $viewAttributes
 * @property string $period
 *
 * @property Person $person
 * @property NormalMemberType $type
 */
class NormalMember extends MemberDbRecord {

    public $count, $year, $countStillMember, $memberType;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'normal_members';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['person_id', 'type_id'], 'required'],
            [['registration', 'deregistration'], 'date', 'format' => 'yyyy-mm-dd'],
            [['person_id', 'type_id'], 'number']
        ];
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

    public function getTypeReadable() {
        $type = $this->type;
        switch ($type->type) {
            case 'bsc':
                $str = 'Bachelor';
                break;
            case 'msc':
                $str = 'Master';
                break;
            default:
                $str = '';
        }

        if ($str != $type->name) {
            $str .= ' ' . $type->name;
        }
        return $str;
    }

    public function getViewAttributes() {
        return $this->getAttributes([
            'id', 'person_id', 'type_id', 'type', 'typeReadable', 'registration', 'deregistration'
        ]);
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
    public function getType() {
        return $this->hasOne(NormalMemberType::className(), ['id' => 'type_id']);
    }

}
