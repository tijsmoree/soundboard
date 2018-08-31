<?php

namespace app\controllers;

use Exception;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\modules\membermodels\models\Board;

class BoardsController extends Controller {

    /**
     * @inheritdoc
     */
    public function beforeAction($action) {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionList() {
        $committees = Board::find()->orderBy('(discharge is null) DESC, discharge DESC')->all();

        return array_map(function ($i) {
            return $i->listAttributes;
        }, $committees);
    }

    public function actionSearch($q, $limit = false) {
        $boards = Board::find()
            ->where(['LIKE', 'number', $q])
            ->orWhere(['LIKE', 'installation', $q])
            ->orderBy('installation DESC');

        if ($limit !== false) {
            $boards->limit($limit);
        }
        $boards = $boards->all();

        return array_map(function ($i) {
            return $i->listAttributes;
        }, $boards);
    }

    public function actionView($id) {
        $item = Board::find()
            ->with(['boardMembers', 'boardMembers.person', 'pictures'])
            ->where(['id' => $id])
            ->one();

        return $item->viewAttributes;
    }

    public function actionSave() {
        $id = Yii::$app->request->post('id');
        if ($id != null) {
            $board = Board::findOne($id);
            if ($board == null) {
                throw new \yii\web\HttpException(404);
            }
            $isNewRecord = false;
        } else {
            $board = new Board();
            $isNewRecord = true;
        }

        $board->setAttributes(Yii::$app->request->post());

        if (!$board->save()) {
            throw new \yii\web\HttpException(422, json_encode($board->errors));
        }

        if ($isNewRecord) {
            Yii::$app->response->statusCode = 201;
            return $board->id;
        }

        return '';
    }

    public function actionDelete() {
        $id = Yii::$app->request->post();

        $board = Board::findOne($id);
        if ($board == null) {
            throw new \yii\web\HttpException(404);
        }

        try {
            foreach ($board->getBoardMembers()->all() as $boardMember) {
                if($boardMember) {
                    $boardMember->delete();
                }
            }
            $board->delete();
        } catch(Exception $e) {
            throw new \yii\web\HttpException(422, json_encode(['message' => [$e->getMessage()]]));
        }
    }

}
