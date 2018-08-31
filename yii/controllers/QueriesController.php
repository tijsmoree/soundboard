<?php

namespace app\controllers;

use PDO;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Writer_CSV;
use Yii;
use app\models\Query;
use yii\base\Exception;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;
use app\modules\membermodels\models\Person;
use app\modules\membermodels\models\AssociateMember;

class QueriesController extends Controller {

    /**
     * @inheritdoc
     */
    public function beforeAction($action) {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionList() {
        $queries = Query::find()->select('*, ifnull(`group`, "") as `group`')
            ->orderBy('(`group` is null or `group` = "") desc, `group`, name')
            ->all();

        return array_map(function ($i) {
            return $i->getAttributes(['id', 'name', 'group', 'query', 'updated_at']);
        }, $queries);
    }

    public function actionGroupList() {
        return array_map(function ($i) {
            return $i->group;
        }, Query::find()
            ->select('group')
            ->distinct()
            ->andWhere('`group` IS NOT NULL')
            ->andWhere('`group` != ""')
            ->andWhere('`group` != "Standaard"')
            ->orderBy('group')
            ->all()
        );
    }

    public function actionTry() {
        $query = Yii::$app->request->post('query', null);
        if ($query == null) {
            return [];
        }

        try {
            return $this->_getQueryResults($query);
        } catch (Exception $e) {
            throw new HttpException(400, $e->getMessage());
        }
    }

    public function actionView($id) {
        $query = Query::findOne($id);
        if ($query == null) {
            throw new HttpException(404, 'Query not found');
        }
        return $query;
    }

    public function actionUpdate($id = null) {
        if ($id == null || $id == 'undefined') {
            $query = new Query();
        } else {
            $query = Query::findOne($id);
            if ($query == null) {
                throw new HttpException(404, 'Query not found');
            }
        }

        $query->attributes = Yii::$app->request->post();
        $query->group = Yii::$app->request->post('group', null);
        if (!$query->save()) {
            Yii::$app->response->setStatusCode(400);
            return [
                'errors' => $query->errors
            ];
        }

        return $query->getAttributes(['id', 'name']);
    }

    public function actionDelete($id) {
        $query = Query::findOne($id);
        return $query->delete();
    }

    public function actionExport($id) {
        $query = Query::findOne($id);
        if ($query == null) {
            throw new HttpException(404);
        }

        $queryResults = $this->_getQueryResults($query->query);

        $objPHPExcel = new PHPExcel();

        // Set properties
        $objPHPExcel->getProperties()
            ->setCreator("ETV Ledendb")
            ->setLastModifiedBy("ETV Ledendb")
            ->setTitle($query->name)
            ->setSubject($query->name);

        $objPHPExcel->setActiveSheetIndex(0);

        $sheet = $objPHPExcel->getActiveSheet();

        $withColumnNames = (count($queryResults['columns']) == count($queryResults['data'][0]));
        $nrColumns = count($queryResults['data'][0]);
        //? $queryResults['columns'] : array_keys($queryResults['data'][0]) ;
        for ($i = 0; $i < $nrColumns; $i++) {
            $sheet->setCellValueByColumnAndRow($i, 1, ($withColumnNames) ? $queryResults['columns'][$i] : 'Kolom ' . ($i+1));
        }

        // Add some data
        $rowI = 2;
        foreach ($queryResults['data'] as $row) {
            $itemI = 0;
            foreach ($row as $item) {
                if (is_array($item)) {
                    $item = json_encode($item);
                }
                $sheet->setCellValueByColumnAndRow($itemI, $rowI, $item);
                $itemI++;
            }
            $rowI++;
        }


        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment;filename="' . $query->name . '.csv"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');

        /** @var PHPExcel_Writer_CSV $objWriter */
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
        $objWriter->setDelimiter(";");

        echo "\xEF\xBB\xBF";
        $objWriter->save('php://output');
    }

    function actionInfo() {
        $tables = ["persons_advanced", "person_addresses", "normal_members", "normal_member_types",
            "honorary_members", "alumni", "associate_members", "board_members", "boards", "board_pictures",
            "committee_members", "committees", "options", "option_person_links",
            "associations", "option_association_links", "person_pictures", "faculty_employees",
            "faculty_departments", "faculty_employments", "room_access", "rooms"];

        $results = [];
        foreach ($tables as $tableName) {
            $columns = Yii::$app->db->createCommand("SHOW COLUMNS FROM " . $tableName)->queryAll();
            foreach ($columns as $i => $column) {
                if (in_array($column['Field'], ['updated_at', 'created_at'])) {
                    unset($columns[$i]);
                }
            }
            $results[$tableName] = $columns;
        }

        return $results;
    }

    private function _getQueryResults($query) {
        $queryObject = Yii::$app->db->createCommand($query);

        $reader = $queryObject->query();
        $reader->setFetchMode(PDO::FETCH_NAMED);

        try {
            $data = $reader->readAll();
        } catch (\PDOException $e) {
            return ['resultExpected' => false]; 
        }
        $columnCount = $reader->getColumnCount();

        $columns = [];
        if (count($data) > 0) {
            $columns = array_keys($data[0]);
        }

        return [
            'resultExpected' => true,
            'columnCount' => $columnCount,
            'columns' => $columns,
            'data' => $data
        ];
    }
}
