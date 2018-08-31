<?php

namespace app\modules\membermodels\models;

/**
 * This is the model class for table "members.titles".
 *
 * @property integer $id
 * @property string $title
 * @property string $form_of_address
 * @property int $rank
 * @property integer $front
 * @property string $created_at
 * @property string $updated_at
 */
class Title extends MemberDbRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'titles';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['title', 'form_of_address', 'rank'], 'required'],
            [['title', 'form_of_address'], 'string'],
            [['rank'], 'number'],
            [['front'], 'boolean']
        ];
    }

    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            $this->form_of_address = ucfirst($this->form_of_address);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTitleLinks() {
        return $this->hasMany(TitleLink::className(), ['title_id' => 'id']);
    }

}
