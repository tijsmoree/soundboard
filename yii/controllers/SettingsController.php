<?php

namespace app\controllers;

use app\modules\membermodels\models\MemberDbRecord;
use app\modules\membermodels\models\Option;
use app\modules\membermodels\models\Room;
use app\modules\membermodels\models\Title;
use Yii;
use yii\base\Exception;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;
use app\modules\membermodels\models\Person;
use app\modules\membermodels\models\CommitteeMember;
use app\modules\membermodels\models\NormalMember;
use app\modules\membermodels\models\NormalMemberType;

class SettingsController extends Controller {

    /**
     * @inheritdoc
     */
    public function beforeAction($action) {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionRead($setting) {
        $items = [];
        $fields = [];
        switch ($setting) {
            case 'normal-members':
                $items = array_map(function ($item) {
                    return $item->getAttributes(['id', 'name', 'type']);
                }, NormalMemberType::find()->orderBy('type, name')->all());
                $fields = [
                    ['attribute' => 'name', 'name' => 'Naam', 'type' => 'string'],
                    ['attribute' => 'type', 'name' => 'Type', 'type' => 'in', 'range' => NormalMemberType::$typeOptions]
                ];
                break;
            case 'options':
                $items = array_map(function ($item) {
                    return $item->getAttributes(['id', 'name', 'description', 'for_persons', 'for_associations']);
                }, Option::find()->orderBy('name')->all());
                $fields = [
                    ['attribute' => 'name', 'name' => 'Naam', 'type' => 'string'],
                    ['attribute' => 'description', 'name' => 'Beschrijving', 'type' => 'string'],
                    ['attribute' => 'for_persons', 'name' => 'Voor personen', 'type' => 'boolean'],
                    ['attribute' => 'for_associations', 'name' => 'Voor verenigingen', 'type' => 'boolean']
                ];
                break;
            case 'titles':
                $items = array_map(function ($item) {
                    return $item->getAttributes(['id', 'title', 'form_of_address', 'rank', 'front']);
                }, Title::find()->orderBy('front DESC, rank, title')->all());
                $fields = [
                    ['attribute' => 'title', 'name' => 'Titel', 'type' => 'string'],
                    ['attribute' => 'form_of_address', 'name' => 'Aanspreek', 'type' => 'string'],
                    ['attribute' => 'rank', 'name' => 'Prioriteit (1 = belangrijkste)', 'type' => 'string'],
                    ['attribute' => 'front', 'name' => 'Voor naam', 'type' => 'boolean']
                ];
                break;
            case 'rooms':
                $items = array_map(function ($item) {
                    return $item->getAttributes(['id', 'name', 'code']);
                }, Room::find()->orderBy('name')->all());
                $fields = [
                    ['attribute' => 'name', 'name' => 'Naam', 'type' => 'string'],
                    ['attribute' => 'code', 'name' => 'Code', 'type' => 'string']
                ];
                break;
        }

        return [
            'items' => $items,
            'fields' => $fields
        ];
    }

    /**
     * We want to return an array like from actionRead, but with items added as wanted.
     *
     * @param $setting
     * @return array
     * @throws HttpException
     */
    public function actionUpdate($setting) {
        $errors = [];

        $items = $this->actionRead($setting);
        $items = $items['items'];

        $updatedItems = Yii::$app->request->post('updated', []);
        $deletedItemIds = Yii::$app->request->post('deleted', []);

        $settings = [
            'normal-members' => ['object' => '\app\modules\membermodels\models\NormalMemberType', 'nameAttribute' => 'name'],
            'options' => ['object' => '\app\modules\membermodels\models\Option', 'nameAttribute' => 'name'],
            'titles' => ['object' => '\app\modules\membermodels\models\Title', 'nameAttribute' => 'title'],
            'rooms' => ['object' => '\app\modules\membermodels\models\Room', 'nameAttribute' => 'name']
        ];

        if (!isset($settings[$setting])) {
            throw new HttpException(404);
        }

        /** @var MemberDbRecord $obj */
        $setting = $settings[$setting];
        $obj = $setting['object'];

        $updatedItemsPerId = [];
        $addedItems = [];
        foreach ($updatedItems as $updatedItem) {
            if (!isset($updatedItem['id'])) {
                // if it is a new item
                $item = new $obj();
                $item->attributes = $updatedItem;
                $newItemReturn = $updatedItem;
                if (!$item->save()) {
                    foreach ($item->getErrors() as $attr => $error) {
                        if (!in_array($errors, $error)) {
                            $errors[] = $item->$setting['nameAttribute'] . ' ' . $item->getFirstError($attr);
                        }
                    }
                } else {
                    $newItemReturn['id'] = $item->id;
                }
                $addedItems[] = $newItemReturn;
            } else {
                // if it is an existing item, buffer the info
                $updatedItemsPerId[$updatedItem['id']] = $updatedItem;
            }
        }

        foreach ($items as $i => $item) {
            if (in_array($item['id'], $deletedItemIds)) {
                $item = $obj::findOne($item['id']);
                try {
                    $item->delete();
                    unset($items[$i]);
                } catch (Exception $e) {
                    $errors[] = $item->$setting['nameAttribute'] . ' kan niet verwijderd worden. Deze wordt nog gebruikt.';
                }
            } elseif (isset($updatedItemsPerId[$item['id']])) {
                $updatedItem = $updatedItemsPerId[$item['id']];
                $item = $obj::findOne($item['id']);
                if ($item == null) {
                    continue;
                }

                $item->attributes = $updatedItem;
                if (!$item->save()) {
                    foreach ($item->getErrors() as $attr => $error) {
                        if (!in_array($errors, $error)) {
                            $errors[] = $item->$setting['nameAttribute'] . ' ' . $item->getFirstError($attr);
                        }
                    }
                }
                $items[$i] = $updatedItem;
            }
        }

        $items = array_merge($items, $addedItems);

        return [
            'errors' => $errors,
            'updatedItems' => $items
        ];
    }

}
