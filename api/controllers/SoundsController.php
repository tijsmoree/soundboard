<?php
namespace app\controllers;

use Yii;
use yii\web\Response;
use yii\web\Controller;
use yii\web\HttpException;
use app\models\Sound;

class SoundsController extends Controller {

  // public function beforeAction($action) {
  //   Yii::$app->response->format = Response::FORMAT_JSON;

  //   $this->enableCsrfValidation = false;
  //   return parent::beforeAction($action);
  // }

  public function actionIndex() {
    return array_map(function ($sound) {
      return $sound->getAttributes([
        'id',
        'name',
        'icon'
      ]);
    }, Sound::find()->all());
  }

  public function actionUpdate($id) {
    $sound = Sound::findOne($id);
    if ($sound == null) {
      $sound = new Sound();
    }

    $sound->setAttributes(Yii::$app->request->post());

    if (!$sound->save()) {
      throw new HttpException(422, json_encode($sound->errors));
    }
  }

  public function actionDelete($id) {
    $sound = Sound::findOne($id);
    if ($sound == null) {
      throw new HttpException(404);
    }

    if (!$sound->delete()) {
      throw new HttpException(422, json_encode($sound->errors));
    }
  }
}
