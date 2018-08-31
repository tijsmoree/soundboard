<?php

namespace app\controllers;

use Exception;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\modules\membermodels\models\FacultyDepartment;

class FacultyDepartmentsController extends Controller {

    /**
     * @inheritdoc
     */
    public function beforeAction($action) {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionList() {
        $departments = FacultyDepartment::find()
                ->with(['activeFacultyEmployments'])
                ->all();

        return array_map(function ($i) {
            return $i->listAttributes;
        }, $departments);
    }

    public function actionSearch($q, $limit = false) {
        $facultyDepartments = FacultyDepartment::find()
            ->where(['LIKE', 'name', $q])
            ->orWhere(['LIKE', 'description', $q]);

        if ($limit !== false) {
            $facultyDepartments->limit($limit);
        }
        $facultyDepartments = $facultyDepartments->all();

        return array_map(function ($i) {
            return $i->listAttributes;
        }, $facultyDepartments);
    }

    public function actionView($id) {
        $item = FacultyDepartment::find()
            //->with(['facultyEmployees', 'facultyEmployees.person'])
            ->where(['id' => $id])
            ->one();

        return $item->viewAttributes;
    }

    public function actionSave() {
        $id = Yii::$app->request->post('id');
        if ($id != null) {
            $facultyDepartment = FacultyDepartment::findOne($id);
            if ($facultyDepartment == null) {
                throw new \yii\web\HttpException(404);
            }
            $isNewRecord = false;
        } else {
            $facultyDepartment = new FacultyDepartment();
            $isNewRecord = true;
        }

        $facultyDepartment->setAttributes(Yii::$app->request->post());

        if (!$facultyDepartment->save()) {
            throw new \yii\web\HttpException(422, json_encode($facultyDepartment->errors));
        }

        if ($isNewRecord) {
            Yii::$app->response->statusCode = 201;
            return $facultyDepartment->id;
        }

        return '';
    }

    public function actionDelete() {
        $id = Yii::$app->request->post();

        $facultyDepartment = FacultyDepartment::findOne($id);
        if ($facultyDepartment == null) {
            throw new \yii\web\HttpException(404);
        }

        try {
            foreach ($facultyDepartment->facultyEmployments as $employment) {
                $employment->delete();
            }
            $facultyDepartment->delete();
        } catch(Exception $e) {
            throw new \yii\web\HttpException(422, json_encode(['message' => [$e->getMessage()]]));
        }
    }

}
