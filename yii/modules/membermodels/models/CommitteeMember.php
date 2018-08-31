<?php

namespace app\modules\membermodels\models;

/**
 * This is the model class for table "members.committee_members".
 *
 * @property integer $id
 * @property integer $person_id
 * @property integer $committee_id
 * @property string $installation
 * @property string $discharge
 * @property integer $function_number
 * @property string $function_name
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Committee $committee
 * @property Person $person
 * @property array $apiInfo
 * @property array $committeeShortName
 */
class CommitteeMember extends MemberDbRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'committee_members';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['person_id', 'committee_id'], 'required'],
            [['function_name'], 'string'],
            [['function_number'], 'number'],
            [['installation', 'discharge'], 'date', 'format' => 'yyyy-mm-dd']
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommittee() {
        return $this->hasOne(Committee::className(), ['id' => 'committee_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerson() {
        return $this->hasOne(Person::className(), ['id' => 'person_id']);
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

    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            if ($this->function_number === '') {
                $this->function_number = null;
            }

            if (!$insert && $this->oldAttributes['discharge'] == '0000-00-00' && $this->discharge == null) {
                $this->discharge = '0000-00-00';
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return array
     */
    public function getApiInfo() {
        return $this->getAttributes([
            'committeeShortName',
            'committeeHasActiveCommitteeMembers',
            'committee_id',
            'function_name',
            'installation',
            'discharge',
        ]);
    }

    /**
     * @return array
     */
    public function getViewAttributes() {
        $result = $this->getAttributes([
            'id',
            'person_id',
            'committee_id',
            'function_number',
            'function_name',
            'installation',
            'discharge',
            'committee'
        ]);
        $result['person'] = $this->person->getAttributes([
            'name',
            'id'
        ]);
        return $result;
    }

    /**
     * @return array
     */
    public function getCommitteeViewAttributes() {
        $result = $this->getAttributes([
            'id',
            'function_number',
            'function_name',
            'installation', // for sorting in view, not to show the value
            'discharge', // for sorting in view, not to show the value
            'period',
            'active'
        ]);
        $result['person'] = $this->person->getAttributes([
            'name',
            'id'
        ]);
        return $result;
    }

    /**
     * @return string
     */
    public function getCommitteeShortName() {
        return $this->committee->short_name;
    }

    public function getCommitteeHasActiveCommitteeMembers() {
        return $this->committee->hasActiveCommitteeMembers;
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
