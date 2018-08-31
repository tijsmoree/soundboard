<?php

namespace app\controllers;

use app\modules\membermodels\models\HonoraryMember;
use app\modules\membermodels\models\NormalMemberType;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\modules\membermodels\models\Committee;

class EnrollmentsController extends Controller {

    /**
     * @inheritdoc
     */
    public function beforeAction($action) {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionGetMemberTypes() {
        $normalMemberTypesRaw = NormalMemberType::find()
            ->orderBy('type asc, name')
            ->all();
        $normalMemberTypes = [];
        foreach ($normalMemberTypesRaw as $item) {
            $normalMemberTypes[] = $item->getAttributes(['id', 'name', 'type']);
        }
        return [
            'honoraryMemberTypes' => HonoraryMember::types(),
            'normalMemberTypes' => $normalMemberTypes
        ];
    }

    public function actionSave() {
        /** @var \yii\db\ActiveRecord $enrollment */
        $request = Yii::$app->request;

        $enrollmentClass = '\app\modules\membermodels\models\\' . $this->_getClassName($request->post('type', ''));
        $enrollmentDateIn = $request->post('enrollment');

        if (isset($enrollmentDateIn['id'])) {
            $enrollment = $enrollmentClass::findOne($enrollmentDateIn['id']);
            if ($enrollment == null || $enrollment->person_id !== $enrollmentDateIn['person_id']) {
                throw new \yii\web\HttpException(404);
            }
            $isNewRecord = false;
        } else {
            $enrollment = new $enrollmentClass();
            $enrollment->person_id = $enrollmentDateIn['person_id'];
            $isNewRecord = true;
        }

        $enrollment->setAttributes($enrollmentDateIn);

        if (!$enrollment->save()) {
            throw new \yii\web\HttpException(422, json_encode($enrollment->errors));
        }

        if ($isNewRecord) {
            Yii::$app->response->statusCode = 201;
            return $enrollment->id;
        }
    }

    public function actionDelete() {
        $request = Yii::$app->request;

        $enrollmentClass = '\app\modules\membermodels\models\\' . $this->_getClassName($request->post('type', ''));
        $id = $request->post('id');

        $enrollment = $enrollmentClass::findOne($id);
        if ($enrollment == null || $enrollment->person_id !== $request->post('person_id')) {
            throw new \yii\web\HttpException(404);
        }

        if (!$enrollment->delete()) {
            throw new \yii\web\HttpException(422, json_encode($enrollment->errors));
        }

    }

    private function _getClassName($type) {
        switch ($type) {
            case 'alumnus':
                return 'Alumnus';
            case 'associate_member':
                return 'AssociateMember';
            case 'honorary_member':
                return 'HonoraryMember';
            case 'normal_member':
                return 'NormalMember';
        }

        throw new \yii\web\HttpException(404, 'Unknown enrollment type');
    }

}
