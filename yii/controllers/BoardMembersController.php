<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\modules\membermodels\models\BoardMember;

class BoardMembersController extends Controller {

    /**
     * @inheritdoc
     */
    public function beforeAction($action) {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionCreate($board_id = null, $person_id = null) {
        $return = [];

        $item = new BoardMember();
        if ($board_id !== null) {
            $item->board_id = (int) $board_id;
            $return['boardMember'] = $item->getAttributes([
                'board_id'
            ]);
            $return['boardMember']['board'] = $item->board->getAttributes([
                'name'
            ]);
        } elseif ($person_id !== null) {
            $item->person_id = (int) $person_id;
            $return['boardMember'] = $item->getAttributes([
                'person_id'
            ]);
            $return['boardMember']['person'] = $item->person->getAttributes([
                'name'
            ]);
        }

        return $return;
    }

    public function actionView($person_id, $board_id) {
        $item = BoardMember::find()
            ->with(['person', 'board'])
            ->where(['person_id' => $person_id, 'board_id' => $board_id])
            ->one();

        return $item->viewAttributes;
    }

    public function actionSave() {
        $personId = Yii::$app->request->post('person_id');
        $boardId = Yii::$app->request->post('board_id');
        $boardMember = BoardMember::find()->where(['person_id' => $personId, 'board_id' => $boardId])->one();
        if ($boardMember == null) {
            $boardMember = new BoardMember();
            $boardMember->person_id = $personId;
            $boardMember->board_id = $boardId;
            $isNewRecord = true;
        } else {
            $isNewRecord = false;
        }

        $boardMember->setAttributes(Yii::$app->request->post());

        if (!$boardMember->save()) {
            throw new \yii\web\HttpException(422, json_encode($boardMember->errors));
        }

        if ($isNewRecord) {
            Yii::$app->response->statusCode = 201;
        }
    }

    public function actionDelete() {
        $personId = Yii::$app->request->post('personId');
        $boardId = Yii::$app->request->post('boardId');
        $boardMember = BoardMember::find()->where(['person_id' => $personId, 'board_id' => $boardId])->one();
        
        if ($boardMember == null) {
            throw new \yii\web\HttpException(404);
        }

        if (!$boardMember->delete()) {
            throw new \yii\web\HttpException(422, json_encode($boardMember->errors));
        }
    }

}
