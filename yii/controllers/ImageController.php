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

class ImageController extends Controller {

    public function actionCommittee($id) {

        $committee = Committee::findOne($id);
        if ($committee === null) {
            throw new HttpException(404);
        }

        $imageFile = $committee->imageFile;
        if ($imageFile === null || !file_exists($imageFile)) {
            throw new HttpException(404);
        }

        header("Pragma: public"); // required
        header("Expires: 0");
        header("Cache-Control: max-age=172800, public, must-revalidate");
        header("Content-Type: image/jpeg");
        header("Content-Disposition: attachment; filename='{$committee->long_name}.jpg';");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . filesize($imageFile));
        flush();
        readfile($imageFile);
        die();
    }

    public function actionPerson($id) {
    	PersonPicture::showOne($id);
    }

    public function actionBoard($picture_id) {

        $picture = BoardPicture::find()->with('board')->andWhere(['id' => $picture_id])->one();
        if ($picture === null) {
            throw new HttpException(404);
        }

        $imageFile = $picture->imageFile;
        if ($imageFile === null || !file_exists($imageFile)) {
            throw new HttpException(404);
        }

        header("Pragma: public"); // required
        header("Expires: 0");
        header("Cache-Control: max-age=172800, public, must-revalidate");
        header("Content-Type: image/jpeg");
        header("Content-Disposition: attachment; filename='{$picture->board->name}.jpg';");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . filesize($imageFile));
        flush();
        readfile($imageFile);
        die();
    }

    public function actionJaarboek() {
        ini_set('max_execution_time', 3000); // 50 minutes
        set_time_limit(3000); // 50 minutes
        // $this->write("Starting...");

        $zipFile = '/tmp/ledenexport.zip';
        $zip = new \ZipArchive();
        $zip->open($zipFile, \ZipArchive::OVERWRITE | \ZipArchive::CREATE);

        $query = Person::find();
        $query->joinWith([
            'normalMembers' => function ($query) {
                $query->andWhere('registration IS NOT NULL')->andWhere('deregistration IS NULL');
            }
        ]);
        $persons = $query->all();
        foreach($persons as $person) {
            $picture = $person->getMainPicture();
            if ($picture !== null) {
                $membership = $person->getNormalMembers()->where([
                    'deregistration' => null
                ])->one();
                $name = $person->last_name .
                    $person->first_name .
                    substr($membership->registration, 0, 4);

                $zip->addFile($picture->getImageFile(), $name . '.jpg');
                // $this->write($name);
            }
        }
        $zip->close();

        header("Pragma: public"); // required
        header("Expires: 0");
        header("Cache-Control: max-age=172800, public, must-revalidate");
        header("Content-Type: application/zip");
        header("Content-Disposition: attachment; filename='ledenexport.zip';");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . filesize($zipFile));
        flush();
        readfile($zipFile);
        die();
        // $this->write("Done");
    }

    private function write($text) {
        echo $text . "<br />";
        ob_flush();
        flush();
    }
}
