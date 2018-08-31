<?php

namespace app\controllers;

use app\modules\membermodels\models\MemberDbRecord;
use app\modules\membermodels\models\Option;
use app\modules\membermodels\models\Person;
use app\modules\membermodels\models\PersonAddress;
use app\modules\membermodels\models\PersonPicture;
use app\modules\membermodels\models\PersonSearch;
use app\modules\membermodels\models\Room;
use app\modules\membermodels\models\Title;
use app\modules\membermodels\models\TitleLink;
use app\modules\membermodels\models\FacultyEmployee;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;

class PersonsController extends Controller {

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
        $matches = PersonSearch::searchAdvanced($q, $limit, true);
        if (empty($matches)) {
            return [];
        }

        $orderBy = "FIELD(id, " . implode($matches, ", ") . ")";
        $results = Person::find()
            ->filterWhere(['in', 'persons.id', $matches])
            ->orderBy([$orderBy => 'DESC'])
            ->all();

        return array_map(function (Person $person) {
            return $person->apiInfo;
        }, $results);
    }

    public function actionUpdate($id) {
        $person = Person::find()->with(['homeAddress', 'parentAddress', 'optionPersonLinks'])->where(['id' => $id])->one();
        $person->setAttributes(Yii::$app->request->post());

        if (!$person->save()) {
            throw new HttpException(422, json_encode($person->errors));
        }

        $facultyEmployee = Yii::$app->request->post('facultyEmployee');
        if($person->facultyEmployee && $facultyEmployee) {
            $person->facultyEmployee->setAttributes($facultyEmployee);
            $person->facultyEmployee->save();
        } elseif ($person->facultyEmployee && !$facultyEmployee) {
            $person->facultyEmployee->delete();

            foreach ($person->facultyEmployments as $employment) {
                $employment->delete();
            }
        } elseif (!$person->facultyEmployee && $facultyEmployee) {
            $newFacultyEmployee = new FacultyEmployee();
            $newFacultyEmployee->setAttributes($facultyEmployee);
            $newFacultyEmployee->save();
        }

        // Save addresses
        foreach (['home', 'parents'] as $type) {
            $attr = ($type == 'home') ? 'homeAddress' : 'parentAddress';

            $inAddress = Yii::$app->request->post($attr);
            if ($person->$attr != null && $inAddress != null) {
                $person->$attr->setAttributes($inAddress);
                $person->$attr->save();
            } elseif ($person->$attr != null && $inAddress == null) {
                $person->$attr->delete();
            } elseif ($person->$attr == null && $inAddress != null) {
                $newAddress = new PersonAddress();
                $newAddress->setAttributes($inAddress);
                $newAddress->person_id = $person->id;
                $newAddress->type = $type;
                $newAddress->save();
            }
        }

        $ticks = [
            'options' => [
                'relationName' => 'optionPersonLinks',
                'childIdAttr' => 'option_id',
                'relationModel' => 'app\modules\membermodels\models\OptionPersonLink',
                'parentIdAttr' => 'person_id'
            ],
            'roomAccess' => [
                'relationName' => 'roomAccess',
                'childIdAttr' => 'room_id',
                'relationModel' => 'app\modules\membermodels\models\RoomAccess',
                'parentIdAttr' => 'person_id'
            ]
        ];

        foreach ($ticks as $type => $obj) {
            $items = Yii::$app->request->post($type, []);
            $selectedItems = [];
            foreach ($items as $item) {
                if ($item['selected']) {
                    $selectedItems[] = $item['id'];
                }
            }
            MemberDbRecord::saveManyToMany($person, $selectedItems, $obj);
        }

        return $this->actionView($id);
    }

    public function actionView($id) {
        $person = Person::find()
            ->with(['committeeMembers', 'committeeMembers.committee', 'optionPersonLinks'])
            ->andWhere(['id' => $id])
            ->one();

        $personOut = $person->getAttributes([
            'id', 'student_number', 'first_name', 'nickname', 'prefix', 'initials', 'last_name', 'sex', 'date_of_birth', 'date_of_death', 'email', 'mobile_phone', 'iban', 'debtor_code', 'building_access', 'comments', 'name', 'formOfAddress', 'nameWithTitle', 'title', 'homeAddress', 'parentAddress', 'draft', 'pictureUrl', 'enrollments', 'pendingChanges', 'facultyEmployee'
        ]);
        $personOut['facultyEmployments'] = [];
        foreach ($person->facultyEmployments as $facultyEmployment) {
            $item = $facultyEmployment->getAttributes(['id', 'installation', 'discharge', 'function', 'period', 'active']);
            $item['facultyDepartment'] = $facultyEmployment->facultyDepartment->getAttributes(['id', 'name', 'description', 'is_support']);
            $personOut['facultyEmployments'][] = $item;
        }
        $personOut['committeeMembers'] = array_map(function ($i) {
            return $i->getAttributes([
                'id',
                'committee',
                'function_number',
                'function_name',
                'installation',
                'discharge',
                'period',
                'active',
            ]);
        }, $person->committeeMembers);
        $personOut['boardMembers'] = [];
        foreach ($person->boardMembers as $boardMember) {
            $item = $boardMember->getAttributes([
                'function_number',
                'function_name',
            ]);
            $item['board'] = $boardMember->board->getAttributes(['id', 'name', 'color']);
            $personOut['boardMembers'][] = $item;
        }

        // Process options
        $selectedOptions = array_map(function ($option) {
            return $option->option_id;
        }, $person->optionPersonLinks);
        $personOut['options'] = array_map(function ($i) use ($selectedOptions) {
            $attrs = $i->getAttributes([
                'id',
                'name',
                'description'
            ]);
            $attrs['selected'] = in_array($attrs['id'], $selectedOptions);
            return $attrs;
        }, Option::find()->where('for_persons = 1')->orderBy('name')->all());

        // Process room access
        $roomAccess = array_map(function ($roomAccess) {
            return $roomAccess->room_id;
        }, $person->roomAccess);
        $personOut['roomAccess'] = array_map(function ($i) use ($roomAccess) {
            $attrs = $i->getAttributes([
                'id',
                'name',
                'code'
            ]);
            $attrs['selected'] = in_array($attrs['id'], $roomAccess);
            return $attrs;
        }, Room::find()->orderBy('name')->all());

        return $personOut;
    }

    public function actionTitles($id) {
        $allTitles = Title::find()->orderBy('front DESC, rank ASC')->all();
        $person = Person::find()->with('titles')->andWhere(['id' => $id])->one();
        $hasTitles = [];
        foreach ($person->titles as $title) {
            $hasTitles[$title->id] = true;
        }

        $titles = [];
        foreach ($allTitles as $title) {
            $titles[] = ['title' => $title->title, 'id' => $title->id, 'selected' => isset($hasTitles[$title->id])];
        }

        return $titles;
    }

    public function actionSaveTitles($id) {
        $person = Person::find()->with('titles')->andWhere(['id' => $id])->one();
        $hasTitles = [];
        foreach ($person->titles as $title) {
            $hasTitles[$title->id] = true;
        }

        $in = Yii::$app->request->post('titleIds');
        foreach ($in as $titleId) {
            if (isset($hasTitles[$titleId])) {
                unset($hasTitles[$titleId]);
            } else {
                $newTitleLink = new TitleLink();
                $newTitleLink->person_id = $person->id;
                $newTitleLink->title_id = $titleId;
                $newTitleLink->save();
            }
        }

        foreach ($hasTitles as $titleId => $selected) {
            TitleLink::deleteAll('person_id = :person_id AND title_id = :title_id', ['person_id' => $person->id, 'title_id' => $titleId]);
        }
    }

    public function actionPictures($id) {
        $person = Person::find()->with('pictures')->andWhere(['id' => $id])->one();

        return array_map(function ($i) {
            return $i->getAttributes([
                'id', 'main', 'file_name', 'url'
            ]);
        }, $person->pictures);
    }

    public function actionPictureMakeMain($id) {
        $picture = PersonPicture::findOne($id);
        if ($picture === null) {
            throw new HttpException(404);
        }

        $picture->makeMain();
    }

    public function actionPictureDelete($id) {
        $picture = PersonPicture::findOne($id);
        if ($picture === null) {
            throw new HttpException(404);
        }

        $picture->delete();
    }

    public function actionCreate() {
        $person = new Person();
        $person->setAttributes(Yii::$app->request->post());

        $person->draft = date('Y-m-d H:i:s');

        if (!$person->save()) {
            throw new HttpException(422, json_encode($person->errors));
        }

        Yii::$app->response->statusCode = 201;
        return $person;
    }

    public function actionDelete($id = null) {
        $person = Person::findOne($id);

        if (!$person->delete()) {
            throw new HttpException(422, json_encode($person->errors));
        }

        return;
    }

    public function actionNoConcept($id = null) {
        $person = Person::findOne($id);
        if ($person === null) {
            throw new HttpException(404);
        }

        $person->draft = null;
        return $person->save();
    }
}
