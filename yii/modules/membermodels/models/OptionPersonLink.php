<?php

namespace app\modules\membermodels\models;

/**
 * This is the model class for table "members.option_person_links".
 *
 * @property integer $person_id
 * @property integer $option_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Person $person
 * @property Option $option
 */
class OptionPersonLink extends MemberDbRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'option_person_links';
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
    public function getOption() {
        return $this->hasOne(Option::className(), ['id' => 'option_id']);
    }

}
