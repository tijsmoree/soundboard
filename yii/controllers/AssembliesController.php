<?php

namespace app\controllers;

use Exception;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\modules\membermodels\models\Board;
use app\modules\membermodels\models\Assembly;
use app\modules\membermodels\models\AssemblyAttendee;

class AssembliesController extends Controller {

    /**
     * @inheritdoc
     */
    public function beforeAction($action) {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionList($board_id = null) {
        $board = Board::findOne($board_id);
        if (!$board)
            $board = Board::find()
                ->orderBy('installation DESC')
                ->one();

        $result = $board->getAttributes([
            'id',
            'name',
            'periodInYears'
        ]);

        $result['assemblies'] = array_map(function ($i) {
            return $i->listAttributes;
        }, $board->assemblies);

        return $result;
    }

    public function actionView($id = null) {
        $assembly = Assembly::find();

        if ($id and $id != 'undefined') {
            $assembly = $assembly->where(['id' => $id]);
        }

        $assembly = $assembly->orderBy('date DESC')->one();
        if ($assembly === null) {
            return [];
        }
        return $assembly->viewAttributes;
    }

    public function actionSave() {
        $id = Yii::$app->request->post('id');
        if ($id != null) {
            $assembly = Assembly::findOne($id);
            if ($assembly == null) {
                throw new \yii\web\HttpException(404);
            }
            $isNewRecord = false;
        } else {
            $assembly = new Assembly();
            $isNewRecord = true;
        }

        $assembly->setAttributes(Yii::$app->request->post());

        if (!$assembly->save()) {
            throw new \yii\web\HttpException(422, json_encode($assembly->errors));
        }

        if ($isNewRecord) {
            Yii::$app->response->statusCode = 201;
            return $assembly->id;
        }

        return '';
    }

    public function actionDelete() {
        $id = Yii::$app->request->post();

        $assembly = Assembly::findOne($id);
        if ($assembly == null) {
            throw new \yii\web\HttpException(404);
        }

        try {
            foreach ($assembly->getAttendees()->all() as $attendee) {
                if($attendee) {
                    $attendee->delete();
                }
            }
            $assembly->delete();
        } catch(Exception $e) {
            throw new \yii\web\HttpException(422, json_encode(['message' => [$e->getMessage()]]));
        }
    }

    public function actionDeleteAttendee() {
        $personId = Yii::$app->request->post('person_id');
        $assemblyId = Yii::$app->request->post('assembly_id');

        $attendee = AssemblyAttendee::findOne([
            'assembly_id' => (int) $assemblyId,
            'person_id' => (int) $personId
        ]);

        return $attendee->delete();
    }

    public function actionAddAttendee() {
        $attendee = AssemblyAttendee::findOne(Yii::$app->request->post());
        if ($attendee) {
            return [
                "success" => false,
                "name" => $attendee->person->name
            ];
        }

        $attendee = new AssemblyAttendee();
        $attendee->setAttributes(Yii::$app->request->post());

        if (!$attendee->save()) {
            throw new \yii\web\HttpException(422, json_encode($attendee->errors));
        } else {
            return [
                "success" => true,
                "name" => $attendee->person->name
            ];
        }
    }

}
