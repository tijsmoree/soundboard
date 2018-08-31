<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\modules\membermodels\models\FacultyEmployee;
use app\modules\membermodels\models\FacultyEmployment;

class FacultyEmploymentsController extends Controller {

    /**
     * @inheritdoc
     */
    public function beforeAction($action) {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionCreate($faculty_department_id = null, $person_id = null) {
        $return = [];

        $item = new FacultyEmployment();
        if ($faculty_department_id !== null) {
            $item->faculty_department_id = (int) $faculty_department_id;
            $return['facultyEmployment'] = $item->getAttributes([
                'faculty_department_id'
            ]);
            $return['facultyEmployment']['facultyDepartment'] = $item->facultyDepartment->getAttributes([
                'name'
            ]);
        } elseif ($person_id !== null) {
            $item->person_id = (int) $person_id;
            $return['facultyEmployment'] = $item->getAttributes([
                'person_id'
            ]);
            $return['facultyEmployment']['person'] = $item->person->getAttributes([
                'name'
            ]);
        }

        return $return;
    }

    public function actionView($id) {
        $item = FacultyEmployment::find()
            ->with(['person', 'facultyDepartment'])
            ->where(['id' => $id])
            ->one();

        return $item->viewAttributes;
    }

    public function actionSave() {
        $id = Yii::$app->request->post('id');
        if ($id != null) {
            $facultyEmployment = FacultyEmployment::findOne($id);
            if ($facultyEmployment == null) {
                throw new \yii\web\HttpException(404);
            }
            $isNewRecord = false;
        } else {
            $facultyEmployment = new FacultyEmployment();
            $isNewRecord = true;
        }

        $facultyEmployment->setAttributes(Yii::$app->request->post());

        if (!$facultyEmployment->save()) {
            throw new \yii\web\HttpException(422, json_encode($facultyEmployment->errors));
        }

        if ($isNewRecord) {
            $facultyEmployee = FacultyEmployee::findOne($facultyEmployment->person_id);

            if($facultyEmployee == null) {
                $facultyEmployee = new FacultyEmployee();
                $facultyEmployee->person_id = $facultyEmployment->person_id;
                
                if (!$facultyEmployee->save()) {
                    throw new \yii\web\HttpException(422, json_encode($facultyEmployee->errors));
                }
            }

            Yii::$app->response->statusCode = 201;
            return $facultyEmployment->id;
        }
    }
    
    public function actionDelete() {
        $request = Yii::$app->request;

        $person_id = $request->post('person_id');
        $faculty_department_id = $request->post('faculty_department_id');

        $facultyEmployment = FacultyEmployment::find()->where(['person_id' => $person_id, 'faculty_department_id' => $faculty_department_id])->one();
        if ($facultyEmployment == null) {
            throw new \yii\web\HttpException(404);
        }

        if (!$facultyEmployment->delete()) {
            throw new \yii\web\HttpException(422, json_encode($facultyEmployment->errors));
        }
    }
}