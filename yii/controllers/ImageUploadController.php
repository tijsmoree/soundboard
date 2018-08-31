<?php

namespace app\controllers;

use app\modules\membermodels\models\BoardPicture;
use app\modules\membermodels\models\Committee;
use app\modules\membermodels\models\PersonPicture;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;
use app\modules\membermodels\models\Person;
use app\modules\membermodels\models\AssociateMember;

class ImageUploadController extends Controller {

    /**
     * @inheritdoc
     */
    public function beforeAction($action) {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionCommittee() {
        $committeeId = Yii::$app->request->post('id');
        $file = $_FILES['file'];

        $committee = Committee::findOne($committeeId);
        if ($committee === null) {
            throw new HttpException(404);
        }

        if (!$committee->saveImage($file)) {
            throw new HttpException(400, 'File could not be saved');
        }
        return true;
    }

    public function actionPerson() {
        $personId = Yii::$app->request->post('id');
        $file = $_FILES['file'];

        $person = Person::findOne($personId);
        if ($person === null) {
            throw new HttpException(404);
        }

        if (!PersonPicture::newPicture($person->id, $file)) {
            throw new HttpException(400, 'File could not be saved');
        }

        return true;
    }

    public function actionBoard() {
        $pictureId = Yii::$app->request->post('picture_id');
        $file = $_FILES['file'];

        if (!BoardPicture::uploadPicture($pictureId, $file)) {
            throw new HttpException(400, 'File could not be saved');
        }


        return true;
    }
}
