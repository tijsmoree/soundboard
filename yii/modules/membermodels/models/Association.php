<?php

namespace app\modules\membermodels\models;

use yii\base\DynamicModel;

/**
 * This is the model class for table "members.associations".
 *
 * @property integer $id
 * @property string $name
 * @property string $type
 * @property string $study
 * @property string $mail_address
 * @property string $mail_postal_code
 * @property string $mail_town
 * @property string $mail_country
 * @property bool $mail_internal
 * @property string $visit_address
 * @property string $visit_postal_code
 * @property string $visit_town
 * @property string $visit_country
 * @property string $phone_number1
 * @property string $phone_number2
 * @property string $fax
 * @property string $email
 * @property string $form_of_address
 * @property string $salutation
 * @property string $website
 * @property string $magazine
 * @property string $commentaar
 * @property string $created_at
 * @property string $updated_at
 *
 * @property OptionAssociationLink[] $optionAssociationLinks
 * @property Option[] $options
 */
class Association extends MemberDbRecord {
    public $query, $searchblob;

    private static $searchColumn = 'CONCAT_WS(" ", name, type, study, mail_address, mail_postal_code, mail_town, mail_country, visit_address,
        visit_postal_code, visit_town, visit_country, phone_number1, phone_number2, fax, email, website, magazine, comments)';

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'associations';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['name'], 'required'],
            [['name', 'type', 'study', 'mail_address', 'mail_postal_code', 'mail_town', 'mail_country', 'visit_address', 'visit_postal_code', 'visit_town', 'visit_country', 'phone_number1',
                'phone_number2', 'fax', 'email', 'form_of_address', 'salutation', 'website', 'magazine', 'comments'], 'string'],
            ['mail_internal', 'boolean']
        ];
    }

    public function beforeSave($insert) {
        $this->comments = $this->comments ?? "";

        return parent::beforeSave($insert);
    } 

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOptionAssociationLinks() {
        return $this->hasMany(OptionAssociationLink::className(), ['association_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOptions() {
        return $this->hasMany(Option::className(), ['id' => 'option_id'])->viaTable('option_association_links', ['association_id' => 'id']);
    }

    /**
     * Search for a person given a text query, return ID's of persons found.
     * Optional limit, limit the results to something else than 10.
     *
     * @param string $query
     * @param int $limit
     * @return array
     */
    public static function search($query, $limit = 10) {
        $model = DynamicModel::validateData(compact('query'), [
            [['query'], 'safe'],
        ]);

        if ($model->hasErrors()) {
            return [];
        }

        $query = Association::find()
            ->select('id')
            ->orderBy('name');

        $searchTerms = explode(" ", $model->query);
        foreach ($searchTerms as $term) {
            $query->AndFilterWhere(['or',
                ['like', static::$searchColumn, $term],
            ]);
        }

        if ($limit !== false) {
            $query->limit($limit);
        }

        return $query->column();
    }

    public function delete() {
        foreach ($this->optionAssociationLinks as $link) {
            $link->delete();
        }

        return parent::delete();
    }
}
