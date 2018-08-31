<?php

namespace app\controllers;

use app\modules\membermodels\models\Association;
use app\modules\membermodels\models\MemberDbRecord;
use app\modules\membermodels\models\Option;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;
use app\modules\membermodels\models\Person;

class AssociationsController extends Controller {

    /**
     * @inheritdoc
     */
    public function beforeAction($action) {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionSearch($q, $limit = false) {
        if ($limit === 'auto') {
            $limit = (strlen($q) > 3) ? false : 15;
        }
        $matches = Association::search($q, $limit);
        if (empty($matches)) {
            return [];
        }

        $orderBy = "FIELD(id, " . implode($matches, ", ") . ")";
        $results = Association::find()
            ->filterWhere(['in', 'id', $matches])
            ->orderBy([$orderBy => 'DESC'])
            ->all();

        return array_map(function ($item) {
            return $item->getAttributes(['id', 'name']);
        }, $results);
    }

    public function actionUpdate($id) {
        $association = Association::findOne($id);
        if ($association == null) {
            throw new HttpException(404);
        }

        $association->setAttributes(Yii::$app->request->post());

        if (!$association->save()) {
            throw new \yii\web\HttpException(422, json_encode($association->errors));
        }

        $items = Yii::$app->request->post('options', []);
        $newOptions = [];
        foreach ($items as $item) {
            if ($item['selected']) {
                $newOptions[] = $item['id'];
            }
        }

        MemberDbRecord::saveManyToMany($association, $newOptions, [
            'relationName' => 'optionAssociationLinks',
            'childIdAttr' => 'option_id',
            'relationModel' => 'app\modules\membermodels\models\OptionAssociationLink',
            'parentIdAttr' => 'association_id'
        ]);

        return $this->actionView($id);
    }

    public function actionView($id) {
        $association = Association::findOne($id);
        $associationOut = $association->attributes;

        // Process options
        $selectedOptions = array_map(function ($option) {
            return $option->option_id;
        }, $association->optionAssociationLinks);
        $associationOut['options'] = array_map(function ($i) use ($selectedOptions) {
            $attrs = $i->getAttributes([
                'id',
                'name',
                'description'
            ]);
            $attrs['selected'] = in_array($attrs['id'], $selectedOptions);
            return $attrs;
        }, Option::find()->where('for_associations = 1')->orderBy('name')->all());

        return $associationOut;
    }

    public function actionCreate() {
        $association = new Association();
        $association->name = 'Nieuwe vereniging';
        $association->save();

        return $association->id;
    }

    public function actionDelete($id = null) {
        $association = Association::findOne($id);

        if (!$association->delete()) {
            throw new \yii\web\HttpException(422, json_encode($association->errors));
        }

        return;
    }

}
