<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;
use app\modules\membermodels\models\Committee;

class CommitteesController extends Controller {

    /**
     * @inheritdoc
     */
    public function beforeAction($action) {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionList() {
        $committees = Committee::find()
                ->with(['activeCommitteeMembers'])
                ->all();

        return array_map(function ($i) {
            return $i->listAttributes;
        }, $committees);
    }

    public function actionSearch($q, $limit = false) {
        $committees = Committee::find()
                ->where(['LIKE', 'short_name', $q])
                ->orWhere(['LIKE', 'long_name', $q]);
        
        if ($limit !== false) {
            $committees->limit($limit);
        }
        $committees = $committees->all();

        return array_map(function ($i) {
            return $i->listAttributes;
        }, $committees);
    }

    public function actionView($id) {
        $item = Committee::find()
                ->with(['committeeMembers', 'committeeMembers.person'])
                ->where(['id' => $id])
                ->one();

        if ($item == null) {
            throw new HttpException(404, 'Committee not found');
        }

        return $item->viewAttributes;
    }

    public function actionSave() {
        $id = Yii::$app->request->post('id');
        if ($id != null) {
            $committee = Committee::findOne($id);
            if ($committee == null) {
                throw new \yii\web\HttpException(404);
            }
            $isNewRecord = false;
        } else {
            $committee = new Committee();
            $isNewRecord = true;
        }

        $committee->setAttributes(Yii::$app->request->post());

        if (!$committee->save()) {
            throw new \yii\web\HttpException(422, json_encode($committee->errors));
        }

        if ($isNewRecord) {
            Yii::$app->response->statusCode = 201;
            return $committee->id;
        }
    }

    /**
     * @param $id Committee id
     * @return bool
     * @throws HttpException
     */
    public function actionRemoveImage($id) {
        $committee = Committee::findOne($id);
        if ($committee == null) {
            throw new \yii\web\HttpException(404);
        }

        return $committee->removeImage();
    }
    
    public function actionDelete() {
        $id = Yii::$app->request->post();
        
        $committee = Committee::findOne($id);
        if ($committee == null) {
            throw new \yii\web\HttpException(404);
        }
        
        if (!$committee->delete()) {
            throw new \yii\web\HttpException(422, json_encode($committee->errors));
        }

    }

}
