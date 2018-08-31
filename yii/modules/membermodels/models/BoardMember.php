<?php

namespace app\modules\membermodels\models;

/**
 * This is the model class for table "members.board_members".
 *
 * @property integer $person_id
 * @property integer $board_id
 * @property string $function_name
 * @property integer $function_number
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Board $board
 * @property Person $person
 */
class BoardMember extends MemberDbRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'board_members';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['person_id', 'board_id'], 'required'],
            [['function_name'], 'string'],
            [['function_number'], 'number']
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBoard() {
        return $this->hasOne(Board::className(), ['id' => 'board_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerson() {
        return $this->hasOne(Person::className(), ['id' => 'person_id']);
    }

    /**
     * @return array
     */
    public function getViewAttributes() {
        $result = $this->getAttributes([
            'person_id',
            'board_id',
            'function_number',
            'function_name',
            'board'
        ]);
        $result['person'] = $this->person->getAttributes([
            'name',
            'id'
        ]);
        $result['board'] = $this->board->getAttributes([
            'name',
            'id'
        ]);
        return $result;
    }

    /**
     * @return array
     */
    public function getboardViewAttributes() {
        $result = $this->getAttributes([
            'function_number',
            'function_name'
        ]);
        $result['person'] = $this->person->getAttributes([
            'name',
            'id'
        ]);
        return $result;
    }
}
