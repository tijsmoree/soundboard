<?php

namespace app\modules\membermodels\models;

use Yii;
use yii\helpers\Url;
use yii\web\HttpException;

/**
 * This is the model class for table "members.board_pictures".
 *
 * @property integer $id
 * @property integer $board_id
 * @property integer $priority
 * @property string $description
 * @property string $file_name
 * @property string $url
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Board $board
 */
class BoardPicture extends MemberDbRecord {
    protected static $maxPictureSize = 2048;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['board_id'], 'required'],
            [['file_name', 'description'], 'string'],
            [['priority', 'board_id'], 'number']
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'board_pictures';
    }

    public function beforeSave($insert) {
        $this->description = $this->description ?? "";

        return parent::beforeSave($insert);
    } 

    public function afterDelete() {
        @unlink($this->imageFile);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBoard() {
        return $this->hasOne(Board::className(), ['id' => 'board_id']);
    }

    /*
     * @return string
     */
    public function getUrl() {
        //TODO: should this go via an etv.tudelft.nl url or a ledendb.etv.tudelft.nl url? Or both?
        return Url::to(['/boards/default/image', 'picture_id' => $this->id]);
    }

    public function getImageUrlBustingCache() {
        return $this->imageUrl . '&time=' . time();
    }

    /*
     * @return string
     */
    public function getImageFile() {
        if (!defined('IMAGE_DIR')) {
            define('IMAGE_DIR', '/mnt/web_content/ledendb');
        }

        return IMAGE_DIR . '/boards/' . $this->board_id . '/' . $this->file_name;
    }

    public static function uploadPicture($pictureId, $file) {
        /** @var BoardPicture $picture */
        $picture = static::findOne($pictureId);
        if ($picture == null) {
            throw new HttpException(404);
        }

        if ($picture->file_name != '') {
            @unlink($picture->imageFile);
        } else {
            $picture->file_name = Yii::$app->security->generateRandomString() . '.jpg';
        }

        if (!file_exists(dirname($picture->imageFile))) {
            mkdir(dirname($picture->imageFile), 0775, true);
        }
        if (!move_uploaded_file($file['tmp_name'], $picture->imageFile)) {
            return false;
        }

        $image = new \Eventviva\ImageResize($picture->imageFile);
        $image->resizeToBestFit(static::$maxPictureSize, static::$maxPictureSize);
        $image->save($picture->imageFile);

        $picture->save();

        return true;
    }
}
