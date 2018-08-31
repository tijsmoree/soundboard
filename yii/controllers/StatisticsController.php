<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\modules\membermodels\models\Person;
use app\modules\membermodels\models\CommitteeMember;
use app\modules\membermodels\models\NormalMember;
use app\modules\membermodels\models\NormalMemberType;

class StatisticsController extends Controller {

    public function actionIndex() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'gender' => $this->_getGenderStats(),
            'enrollments' => $this->_getEnrollmentsStats(),
            'committeeMembers' => $this->_getCommitteeMembersStats(),
            'prenames' => $this->_getPrenamesStats()
        ];
    }

    private function _getGenderStats() {
        $stats = Person::find()
            ->joinWith('normalMembers')
            ->andWhere('deregistration IS NULL')
            ->andWhere('registration IS NOT NULL')
            ->select(['sex', 'count(*) AS count'])
            ->groupBy('sex')
            ->all();

        $result = [];
        foreach ($stats as $item) {
            $result[$item->sex] = (int) $item->count;
        }
        return $result;
    }

    private function _getEnrollmentsStats() {
        $statsQuery = NormalMember::find()
            ->joinWith('type')
            ->select(['YEAR(registration) AS year', NormalMemberType::tableName() . '.type AS memberType', 'count(DISTINCT person_id) AS count', 'count(DISTINCT IF(deregistration IS NULL, person_id, NULL)) AS countStillMember'])
            ->andWhere('type_id IS NOT NULL')
            ->groupBy(NormalMemberType::tableName() . '.type, YEAR(registration)')
            ->all();

        $columns = ['bsc' => 'Bachelor', 'msc' => 'Master', 'other' => 'Overig'];
        $statsTemp = [];
        foreach ($statsQuery as $row) {
            $statsTemp[$row->year][$row->memberType] = [$row->countStillMember, $row->count];
        }
        
        krsort($statsTemp);
        $stats = [];
        foreach ($statsTemp as $year => $row) {
            $stats[] = ['year' => $year, 'data' => $row];
        }

        return [
            'stats' => $stats,
            'columns' => $columns
        ];
    }

    private function _getCommitteeMembersStats() {
        $stats = Person::find()
            ->select([Person::tableName() . '.id', 'first_name', 'nickname', 'prefix', 'last_name', 'count(DISTINCT ' . CommitteeMember::tableName() . '.id) AS count'])
            ->joinWith('normalMembers')
            ->joinWith('committeeMembers')
            ->joinWith('committeeMembers.committee')
            ->andWhere('deregistration IS NULL')
            ->andWhere('registration IS NOT NULL')
            ->andWhere('type = "normal"')
            ->groupBy(Person::tableName() . '.id')
            ->orderBy('count DESC')
            ->limit(15)
            ->all();

        return array_map(function ($i) {
            return $i->getAttributes([
                    'id', 'name', 'count'
            ]);
        }, $stats);
    }

    private function _getPrenamesStats() {
        $stats = Person::find()
            ->select(['first_name', 'count(*) AS count'])
            ->joinWith('normalMembers')
            ->andWhere('deregistration IS NULL')
            ->andWhere('registration IS NOT NULL')
            ->andWhere('first_name != ""')
            ->groupBy('first_name')
            ->orderBy('count(*) DESC')
            ->limit(15)
            ->all();

        $result = [];
        foreach ($stats as $item) {
            $result[$item->first_name] = (int) $item->count;
        }
        return $result;
    }

}
