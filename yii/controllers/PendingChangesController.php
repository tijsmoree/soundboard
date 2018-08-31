<?php

namespace app\controllers;

use app\modules\membermodels\models\PendingChange;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;

class PendingChangesController extends Controller {

	/**
	 * @inheritdoc
	 */
	public function beforeAction($action) {
		Yii::$app->response->format = Response::FORMAT_JSON;

		$this->enableCsrfValidation = false;
		return parent::beforeAction($action);
	}

	public function actionIndex() {
		$amount = PendingChange::find()->where(['resolved_resolution' => NULL])->count();

		return array_map(function($change) {
			return $change->apiInfo;
		},
		PendingChange::find()
			->orderBy("`resolved_resolution` IS NULL DESC, `resolved_at` DESC")
			->limit($amount + 5)
			->all()
		);
	}

	public function actionView($id) {
		return $this->findModel($id)->apiInfo;
	}

	public function actionLatest() {
		return PendingChange::find()
			->orderBy(['created_at' => SORT_DESC])
			->one()
			->apiInfo;
	}

	public function actionAccept($id) {
		$model = $this->findModel($id);
		$model->accept();
		return [];
	}

	public function actionDecline($id) {
		/* @var PendingChange $model */
		$model = $this->findModel($id);
		$model->decline();
		return [];
	}

	public function findModel($id) {
		$model = PendingChange::find()->where(['id' => $id])->one();
		if($model === null) {
			throw new HttpException(404);
		}
		return $model;
	}
}
