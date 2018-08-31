<?php

namespace app\modules\membermodels\models;

/**
 * This is the model class for table "members.options".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 *
 * @property OptionAssociationLink[] $optionAssociationLinks
 * @property Association[] $associations
 * @property OptionPersonLink[] $optionPersonLinks
 * @property Person[] $people
 */
class Option extends MemberDbRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'options';
    }


    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['name', 'description'], 'required'],
            [['name', 'description'], 'string'],
            [['for_persons', 'for_associations'], 'number']
        ];
    }

    public function beforeSave($insert) {
        $this->description = $this->description ?? "";

        return parent::beforeSave($insert);
    } 

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOptionAssociationLinks() {
        return $this->hasMany(OptionAssociationLink::className(), ['option_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAssociations() {
        return $this->hasMany(Association::className(), ['id' => 'association_id'])->viaTable('option_association_links', ['option_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOptionPersonLinks() {
        return $this->hasMany(OptionPersonLink::className(), ['option_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeople() {
        return $this->hasMany(Person::className(), ['id' => 'person_id'])->viaTable('option_person_links', ['option_id' => 'id']);
    }

}
