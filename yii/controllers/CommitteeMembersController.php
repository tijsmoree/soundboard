<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\modules\membermodels\models\CommitteeMember;

class CommitteeMembersController extends Controller {

    /**
     * @inheritdoc
     */
    public function beforeAction($action) {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionCreate($committee_id = null, $person_id = null) {
        $return = [];

        $item = new CommitteeMember();
        if ($committee_id !== null) {
            $item->committee_id = (int) $committee_id;
            $return = $item->getAttributes([
                'committee_id',
                'committee'
            ]);
        } elseif ($person_id !== null) {
            $item->person_id = (int) $person_id;
            $return = $item->getAttributes([
                'person_id'
            ]);
            $return['person'] = $item->person->getAttributes([
                'name'
            ]);
        }

        return $return;
    }

    public function actionView($id) {
        $item = CommitteeMember::find()
                ->with(['person', 'committee'])
                ->where(['id' => $id])
                ->one();

        return $item->viewAttributes;
    }

    public function actionSave() {
        $id = Yii::$app->request->post('id');
        if ($id != null) {
            $committeeMember = CommitteeMember::findOne($id);
            if ($committeeMember == null) {
                throw new \yii\web\HttpException(404);
            }
            $isNewRecord = false;
        } else {
            $committeeMember = new CommitteeMember();
            $isNewRecord = true;
        }

        $committeeMember->setAttributes(Yii::$app->request->post());

        if (!$committeeMember->save()) {
            throw new \yii\web\HttpException(422, json_encode($committeeMember->errors));
        }

        if ($isNewRecord) {
            Yii::$app->response->statusCode = 201;
            return $committeeMember->id;
        }
    }

    public function actionDelete() {
        $id = Yii::$app->request->post();

        $committeeMember = CommitteeMember::findOne($id);
        if ($committeeMember == null) {
            throw new \yii\web\HttpException(404);
        }

        if (!$committeeMember->delete()) {
            throw new \yii\web\HttpException(422, json_encode($committeeMember->errors));
        }
    }

}
