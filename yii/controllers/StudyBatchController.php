<?php

namespace app\controllers;

use PDO;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Writer_CSV;
use app\modules\membermodels\models\Person;
use app\modules\membermodels\models\NormalMember;
use app\modules\membermodels\models\NormalMemberType;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class StudyBatchController extends Controller {

    /**
     * @inheritdoc
     */
    public function beforeAction($action) {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionTypes() {
        $normalMemberTypesRaw = NormalMemberType::find()
            ->orderBy('type asc, name')
            ->all();
        $normalMemberTypes = [];
        foreach ($normalMemberTypesRaw as $item) {
            $normalMemberTypes[] = $item->getAttributes(['id', 'name', 'type']);
        }
        return $normalMemberTypes;
    }

    public function actionStudyNumbers() {
        $normalMembers = NormalMember::find()
            ->where(['deregistration' => null])
            ->all();

        $studyNumbers = array_map(function ($nm) {
            return $nm->person->student_number;
        }, $normalMembers);

        $objPHPExcel = new PHPExcel();

        $objPHPExcel->getProperties()
            ->setCreator("ETV Ledendb")
            ->setLastModifiedBy("ETV Ledendb")
            ->setTitle("studienummersETV")
            ->setSubject("studienummersETV");

        $objPHPExcel->setActiveSheetIndex(0);

        $sheet = $objPHPExcel->getActiveSheet();

        $sheet->setCellValueByColumnAndRow(0, 1, 'studienummer');
        $sheet->setCellValueByColumnAndRow(1, 1, 'studie');

        $row = 2;
        foreach ($studyNumbers as $sn) {
            $sheet->setCellValueByColumnAndRow(0, $row, $sn);
            $row++;
        }

        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment;filename="studienummersETV.csv"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
        $objWriter->setDelimiter(";");

        echo "\xEF\xBB\xBF";
        $objWriter->save('php://output');
    }

    public function actionDiff() {
        $numberStudies = Yii::$app->request->post();

        $result = array_map(function ($s) use ($numberStudies) {
            $people = [];

            foreach ($numberStudies as $student_number => $study_id) {
                if ($s == $study_id) {
                    $person = Person::find()
                        ->where(['student_number' => $student_number])
                        ->one();

                    foreach ($person->normalMembers as $study) {
                        if (!$study->deregistration && $study->type_id != $study_id) {
                            if ($study->type->type != "other") {
                                $studyName = $study->type->name . " (" . $study->type->type . ")";
                            } else {
                                $studyName = $study->type->name;
                            }
                            $people[] = [
                                "id" => $person->id,
                                "name" => $person->name,
                                "old_study" => $studyName
                            ];
                        }
                    }
                }
            }

            if ($s) {
                $normalMember = NormalMemberType::find()
                    ->where(["id" => $s])
                    ->one();

                if ($normalMember->type != "other") {
                    $studyName = $normalMember->name . " (" . $normalMember->type . ")";
                } else {
                    $studyName = $normalMember->name;
                }
            } else {
                $studyName = "Uitschrijven";
            }

            if (!empty($people)) {
                return [
                    "id" => $s,
                    "name" => $studyName,
                    "people" => $people
                ];
            }
        }, array_unique(array_values($numberStudies)));

        return array_values(array_filter($result));
    }

    public function actionUpdate($id) {
        $personIds = Yii::$app->request->post();

        foreach ($personIds as $person_id) {
            $study = NormalMember::find()
                ->where(["person_id" => $person_id])
                ->andWhere(["deregistration" => NULL])
                ->orderBy(["registration" => SORT_DESC])
                ->one();

            $study->deregistration = date("Y-m-d");
            $study->save();

            if ($id) {
                $newStudy = new NormalMember();
                $newStudy->person_id = $person_id;
                $newStudy->type_id = $id;
                $newStudy->registration = date("Y-m-d");
                $newStudy->save();
            }
        }

        return true;
    }
}