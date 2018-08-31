<?php

namespace app\modules\membermodels\models;

use Yii;

/**
 * This is the model class for table "members.honorary_members".
 *
 * @property integer $id
 * @property integer $person_id
 * @property string $type
 * @property string $typeReadable
 * @property string $installation
 * @property string $discharge
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 *
 * @property array $viewAttributes
 * @property string $period
 *
 * @property Person $person
 * @property array $siteInfo
 * @property string $slug
 */
class HonoraryMember extends MemberDbRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'honorary_members';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['person_id', 'type', 'installation'], 'required'],
            [['type'], 'in', 'range' => ['evv', 'evb', 'erevoorzitter', 'lvv']],
            [['description'], 'string'],
            [['installation', 'discharge'], 'date', 'format' => 'yyyy-mm-dd'],
            [['person_id'], 'number']
        ];
    }

    public function beforeSave($insert) {
        $this->description = $this->description ?? "";

        return parent::beforeSave($insert);
    } 

    /**
     * @param string $string
     * @return string
     */
    private function _slug($string) {
        return preg_replace('/\s+/', '-', strtolower(preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities($string))));
    }

    /**
     * @return string
     */
    public function getSlug() {
        $names = [
            $this->_slug($this->person->prefix),
            $this->_slug($this->person->last_name)
        ];

        $names = array_filter($names);

        return implode('-', $names);
    }

    private function formatDate($date, $start) {
        return is_null($date) || strpos($date, '0000-00-00') !== false ?
            ($start ? Yii::t('app', 'Onbekend') : Yii::t('app', 'Heden')) :
            Yii::$app->formatter->asDate($date);
    }

    public function getPeriod() {
        return implode(' - ', [
            $this->formatDate($this->installation, true),
            $this->formatDate($this->discharge, false)
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerson() {
        return $this->hasOne(Person::className(), ['id' => 'person_id']);
    }

    public static function types() {
        return [
            [
                'id' => 'erevoorzitter',
                'name' => 'Erevoorzitter'
            ],
            [
                'id' => 'evv',
                'name' => 'Erelid van Vereeniging'
            ],
            [
                'id' => 'evb',
                'name' => 'Erelid van Bestuur'
            ],
            [
                'id' => 'lvv',
                'name' => 'Lid van Verdienste'
            ]
        ];
    }


    public function getTypeReadable() {
        $types = static::types();
        foreach ($types as $type) {
            if ($this->type == $type['id']) {
                return $type['name'];
            }
        }
        return null;
    }

    public function getViewAttributes() {
        return $this->getAttributes([
            'id', 'person_id', 'type', 'typeReadable', 'installation', 'discharge', 'description'
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function getDb() {
        return Yii::$app->getModule('membermodels')->getDb();
    }
}
