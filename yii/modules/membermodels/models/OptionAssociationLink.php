<?php

namespace app\modules\membermodels\models;

/**
 * This is the model class for table "members.option_association_links".
 *
 * @property integer $association_id
 * @property integer $option_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Association $association
 * @property options $option
 */
class OptionAssociationLink extends MemberDbRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'option_association_links';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAssociation() {
        return $this->hasOne(Association::className(), ['id' => 'association_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOption() {
        return $this->hasOne(Option::className(), ['id' => 'option_id']);
    }

}
