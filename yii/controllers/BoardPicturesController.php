<?php

namespace app\controllers;

use app\modules\membermodels\models\Board;
use app\modules\membermodels\models\BoardPicture;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;
use app\modules\membermodels\models\Committee;

class BoardPicturesController extends Controller {

    /**
     * @inheritdoc
     */
    public function beforeAction($action) {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionSave() {
        $id = Yii::$app->request->post('id');
        if ($id != null) {
            $picture = BoardPicture::findOne($id);
            if ($picture == null) {
                throw new \yii\web\HttpException(404);
            }
        } else {
            $picture = new BoardPicture();
            $picture->priority = -1;
        }

        $picture->setAttributes(Yii::$app->request->post());

        if (!$picture->save()) {
            throw new \yii\web\HttpException(422, json_encode($picture->errors));
        }

        return $picture->id;
    }

    public function actionSaveOrder() {
        $id = Yii::$app->request->post('board_id');
        if ($id == null) {
            throw new HttpException(404);
        }

        $board = Board::find()->with('pictures')->where(['id' => $id])->one();
        if ($board == null) {
            throw new HttpException(404);
        }

        $picIds = array_flip(Yii::$app->request->post('picture_ids', []));
        foreach ($board->pictures as $pic) {
            $pic->priority = (isset($picIds[$pic->id])) ? $picIds[$pic->id] : -1;
            $pic->save();
        }

    }
    
    public function actionDelete() {
        $id = Yii::$app->request->post();
        
        $picture = BoardPicture::findOne($id);
        if ($picture == null) {
            throw new \yii\web\HttpException(404);
        }
        
        if (!$picture->delete()) {
            throw new \yii\web\HttpException(422, json_encode($picture->errors));
        }

    }

}
