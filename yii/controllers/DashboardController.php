<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\modules\membermodels\models\Person;
use app\modules\membermodels\models\AssociateMember;

class DashboardController extends Controller {

    /**
     * @inheritdoc
     */
    public function beforeAction($action) {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionIndex() {
        return [
            'drafts' => $this->_getDraftsInfo(),
            'birthdays' => $this->_getBirthdays(),
            'associateMembers' => $this->_getAssociateMembersInfo()
        ];
    }

    private function _getDraftsInfo() {
        $info = Person::find()->andWhere('draft IS NOT NULL')->all();

        return array_map(function ($i) {
            return $i->getAttributes(['id', 'name', 'draft']);
        }, $info);
    }

    private function _getBirthdays() {
        $info = Person::getBirthdays(7, 2);

        return array_map(function ($i) {
            return $i->getAttributes(['id', 'name', 'date_of_birth', 'birthday']);
        }, $info);
    }

    private function _getAssociateMembersInfo() {
        $info = AssociateMember::find()
            ->andWhere('deregistration IS NULL')
            ->orderBy('(expiration = "0000-00-00") ASC, expiration ASC')
            ->all();

        return array_map(function ($i) {
            $item = $i->getAttributes(['registration', 'expiration']);
            $item['person'] = $i->person->getAttributes(['id', 'name']);
            return $item;
        }, $info);
    }

}
