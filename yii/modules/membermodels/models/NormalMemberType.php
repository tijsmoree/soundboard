<?php

namespace app\modules\membermodels\models;

/**
 * This is the model class for table "members.normal_member_types".
 *
 * @property integer $id
 * @property string $name
 * @property string $type
 * @property string $created_at
 * @property string $updated_at
 *
 * @property NormalMember[] $normalMembers
 */
class NormalMemberType extends MemberDbRecord {
    static $typeOptions = ['bsc' => 'Bachelor', 'msc' => 'Master', 'other' => 'Overig'];

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['name', 'type'], 'required'],
            [['name'], 'string'],
            [['type'], 'in', 'range' => array_keys(static::$typeOptions)]
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'normal_member_types';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNormalMembers() {
        return $this->hasMany(NormalMember::className(), ['type_id' => 'id']);
    }

}
