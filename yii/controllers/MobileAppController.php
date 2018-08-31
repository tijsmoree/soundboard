<?php

namespace app\controllers;

use app\modules\membermodels\models\PersonPicture;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\modules\membermodels\models\PersonSearch;
use app\modules\membermodels\models\Person;

class MobileAppController extends Controller {

    /**
     * @inheritdoc
     */
    public function beforeAction($action) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: accept, content-type, dbaccess-token, dbaccess-site');

        if (Yii::$app->request->isOptions) {
            Yii::$app->end();
        }

        Yii::$app->etvipAccess->beforeAction($action);

        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionSearch($q = null) {
	return Yii::$app->runAction('persons/search', ['q' => $q, 'limit' => 20]);
    }

    public function actionPerson($id) {
        $person = Person::find()
            ->with(['committeeMembers', 'committeeMembers.committee', 'boardMembers', 'boardMembers.board'])
            ->andWhere(['id' => $id])
            ->one();

        if ($person === null) {
            throw new \yii\web\HttpException(404);
        }

        $personOut = $person->getAttributes([
            'id', 'student_number', 'first_name', 'nickname', 'prefix', 'initials', 'last_name', 'sex', 'date_of_birth', 'date_of_death', 'email', 'mobile_phone', 'iban', 'debtor_code', 'building_access', 'comments', 'name', 'formOfAddress', 'nameWithTitle', 'title', 'homeAddress', 'parentAddress', 'draft', 'pictureUrl'
        ]);
        $personOut['committeeMembers'] = array_map(function ($i) {
            return $i->getAttributes([
                    'id',
                    'committee',
                    'function_number',
                    'function_name',
                    'installation',
                    'discharge',
                    'active',
            ]);
        }, $person->committeeMembers);
        $personOut['boardMembers'] = [];
        foreach ($person->boardMembers as $boardMember) {
            $item = $boardMember->getAttributes([
                'function_number',
                'function_name',
            ]);
            $item['board'] = $boardMember->board->getAttributes(['id', 'number', 'periodInYears', 'installation', 'color']);
            $personOut['boardMembers'][] = $item;
        }

        return $personOut;
    }

	public function actionPersonImage($id) {
		PersonPicture::showOne($id);
	}

	public function actionUploadPersonImage() {
	Yii::$app->runAction('image-upload/person');
        die('DO IT!!');
    }

}
