<?php

namespace app\modules\membermodels\models;

use yii\helpers\Url;
use yii\web\HttpException;

/**
 * This is the model class for table "members.person_pictures".
 *
 * @property integer $id
 * @property integer $person_id
 * @property integer $main
 * @property string $file_name
 * @property string $created_at
 * @property string $updated_at
 *
 * @property string url
 * @property string urlBustingCache
 * @property string imageDirectory
 * @property string imageFile
 * @property bool isOldStylePicture
 * @property Person $person
 */
class PersonPicture extends MemberDbRecord {
    public static $maxPictureSize = 1024;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'person_pictures';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerson() {
        return $this->hasOne(Person::className(), ['id' => 'person_id']);
    }

    public function getIsOldStylePicture() {
        return (strpos($this->file_name, 'LedenFotos/') === 0);
    }

    public function getUrl() {
        return Url::to(['/members/person/image', 'id' => $this->id]);
    }

    public function getUrlBustingCache() {
        return $this->url . '&time=' . time();
    }

    public function getImageDirectory() {
        if (!defined('IMAGE_DIR')) {
            define('IMAGE_DIR', '/mnt/web_content/ledendb');
        }
        if ($this->isOldStylePicture) {
            $imageFilePath = IMAGE_DIR . "/persons/by_name_and_year";
        } else {
            $imageFilePath = IMAGE_DIR . "/persons/{$this->person_id}";
            if (!file_exists($imageFilePath)) {
                mkdir($imageFilePath, 0775, true);
            }
        }
        return $imageFilePath;
    }

    public function getImageFile() {
        if ($this->isOldStylePicture) {
            $name = static::getFileNameOfOldFile($this->file_name, true);
            return $this->imageDirectory . "/" . $name;
        } else {
            return $this->imageDirectory . "/" . $this->file_name;
        }
    }

    public function newFileName($name) {
        $appendix = 1;

        $name = strtolower($name);

        $parts = explode('.', $name);
        $ext = array_pop($parts);
        $name = implode('.', $parts);

        $name = str_replace([' '], ['_'], $name);
        while (file_exists($this->imageDirectory . "/" . $name . '.' . $ext)) {
            if ($appendix > 1) {
                $name = substr($name, 0, -1 * (strlen($appendix) + 1));
            }
            $name = $name . '-' . ($appendix++);
        }
        return $name . '.' . $ext;
    }

    public function makeMain($cleanUp = true) {
        if ($cleanUp) {
            $mainNow = static::find()->where(['person_id' => $this->person_id, 'main' => 1])->all();
            foreach ($mainNow as $item) {
                $item->main = 0;
                $item->save();
            }
        }

        $this->main = 1;
        $this->save();
    }

    public static function newPicture($personId, $file) {
        $newPicture = new static();
        $newPicture->person_id = $personId;
        $newPicture->file_name = $newPicture->newFileName($file['name']);

        if (!move_uploaded_file($file['tmp_name'], $newPicture->imageFile)) {
            return false;
        }

        $image = new \Eventviva\ImageResize($newPicture->imageFile);
        $image->resizeToBestFit(static::$maxPictureSize, static::$maxPictureSize);
        $image->save($newPicture->imageFile);

        $newPicture->save();
        $newPicture->makeMain();

        return true;
    }

    public static function getFileNameOfOldFile($name, $withExtension = false) {
        $part = explode('/', $name);
        $part = $part[1];
        if ($withExtension) {
            return $part;
        }

        $part = explode('.', $part);
        return $part[0];
    }

    public static function showOne($id) {
        $picture = static::findOne($id);
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
        header("Content-Disposition: attachment; filename='{$picture->file_name}';");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . filesize($imageFile));
        flush();
        readfile($imageFile);
        die();
    }

    public function delete() {
        $imageFile = $this->imageFile;
        if (file_exists($imageFile)) {
            unlink($this->imageFile);
        }

        if ($this->main) {
            $newMain = static::find()
                ->orderBy('created_at DESC')
                ->andWhere(['=', 'person_id', $this->person_id])
                ->andWhere(['<>', 'id', $this->id])->one();
            if ($newMain !== null) {
                $newMain->makeMain(false);
            }
        }

        return parent::delete();
    }
}
