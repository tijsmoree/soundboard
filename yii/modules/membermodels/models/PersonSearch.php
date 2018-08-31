<?php

namespace app\modules\membermodels\models;

use Yii;
use yii\base\DynamicModel;

/**
 * This is the model class for table "members.persons_search".
 *
 * @property integer $id
 * @property string $title
 * @property string $first_name
 * @property string $nickname
 * @property string $initials
 * @property string $prefix
 * @property string $last_name
 * @property string $email
 * @property string $mobile_phone
 * @property string $work_phone
 * @property string $address
 * @property string $postal_code
 * @property string $town
 * @property string $country
 * @property string $phone_number
 */
class PersonSearch extends MemberDbRecord {

    private static $searchColumnName = 'searchblob';

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'persons_search';
    }

    public static function memberSearch($query, $limit = 10) {
        return static::search($query, $limit, true, true);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNormalMembers() {
        return $this->hasMany(NormalMember::className(), ['person_id' => 'id']);
    }

    public static function searchAdvanced($query, $limit = 10, $orderByRelevance = false, $onlyMembers = false) {
        $tempSearchColumnName = static::$searchColumnName;
        static::$searchColumnName = 'searchblob_advanced';
        $results = static::search($query, $limit, $orderByRelevance, $onlyMembers);
        static::$searchColumnName = $tempSearchColumnName;

        return $results;
    }

    /**
     * Search for a person given a text query, return ID's of persons found.
     * Optional limit, limit the results to something else than 10.
     *
     * @param string $query
     * @param int $limit
     * @return array
     */
    public static function search($query, $limit = 10, $orderByRelevance = false, $onlyMembers = false) {
        $model = DynamicModel::validateData(compact('query'), [
            [['query'], 'safe'],
        ]);

        if ($model->hasErrors()) {
            return [];
        }

        $query = PersonSearch::find();

        $searchTerms = explode(" ", $model->query);
        foreach ($searchTerms as $term) {
            $query->AndFilterWhere(['or',
                ['like', static::$searchColumnName, $term],
            ]);
        }

        if ($limit !== false) {
            $query->limit($limit);
        }

        if ($orderByRelevance !== false) {
            $query->orderBy('relevance DESC');
        }

        if ($onlyMembers !== false) {
            $query->joinWith([
                'normalMembers' => function ($query) {
                    $query->andWhere('registration IS NOT NULL')->andWhere('deregistration IS NULL');
                }
            ]);
        }

        return $query->select(['persons_search.id', 'relevance'])->distinct()->column();
    }

}
