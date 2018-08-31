<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;
use app\modules\membermodels\models\Person;
use app\modules\membermodels\models\AssociateMember;

class DefaultController extends Controller {
	public function actionGetUser() {
        Yii::$app->response->format = Response::FORMAT_JSON;
		
		$info = ['name' => 'User', 'email' => null];
		if (isset($_SESSION['db_access'])) {
			$info['name'] = $_SESSION['db_access']['name'];
			$info['email'] = $_SESSION['db_access']['email'];
		}

		return $info;
	}
	
    public function actionEtvip() {
        $fileName = 'etvip-1.4.0.apk';

        $file = dirname(Yii::$app->basePath) . '/files/etvip/' . $fileName;
        if (!file_exists($file)) {
            throw new HttpException(404, 'File not found');
        }

        header("Pragma: public"); // required
        header("Expires: 0");
        header("Cache-Control: no-cache, must-revalidate");
        header("Content-Type: application/vnd.android.package-archive");
        header("Content-Disposition: attachment; filename=\"" . $fileName . "\"");
        header("Content-Transfer-Encoding: binary");
        readfile($file);
    }
}