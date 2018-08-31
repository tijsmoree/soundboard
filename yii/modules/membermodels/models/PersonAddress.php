<?php

namespace app\modules\membermodels\models;

use Yii;
/**
 * This is the model class for table "members.person_addresses".
 *
 * @property integer $id
 * @property integer $person_id
 * @property string $type
 * @property string $address
 * @property string $postal_code
 * @property string $town
 * @property string $country
 * @property string $phone_number
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Person $person
 * @property array $apiInfo
 */
class PersonAddress extends MemberDbRecord {

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'person_id' => Yii::t('app', 'Persoon nummer'),
            'type' => Yii::t('app', 'Type'),
            'address' => Yii::t('app', 'Straat + huisnummer'),
            'postal_code' => Yii::t('app', 'Postcode'),
            'town' => Yii::t('app', 'Plaats'),
            'country' => Yii::t('app', 'Land'),
            'phone_number' => Yii::t('app', 'Telefoon nummer'),
            'created_at' => Yii::t('app', 'Aangemaakt op'),
            'updated_at' => Yii::t('app', 'Bijgewerkt op'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'person_addresses';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['address', 'postal_code', 'town', 'country', 'phone_number'], 'string'],
            [['type'], 'in', 'range' => ['home', 'parents']],
        ];
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
    public function getApiInfo() {
        return $this->getAttributes([
                'type',
                'address',
                'postal_code',
                'town',
                'country',
                'phone_number',
        ]);
    }
}
