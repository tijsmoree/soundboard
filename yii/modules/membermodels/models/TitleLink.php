<?php

namespace app\modules\membermodels\models;

/**
 * This is the model class for table "members.title_link".
 *
 * @property integer $person_id
 * @property integer $title_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Person $person
 * @property Title $title
 */
class TitleLink extends MemberDbRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'title_person_links';
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
    public function getTitle() {
        return $this->hasOne(Title::className(), ['id' => 'title_id']);
    }

}
