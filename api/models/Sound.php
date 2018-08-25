<?php

namespace api\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "sounds.sounds".
 *
 * @property integer $id
 * @property string $name
 * @property string $icon
 * @property string $updated_at
 * @property string $created_at
 */
class Sound extends ActiveRecord {

  public function behaviors() {
    return [
      [
        'class' => TimestampBehavior::className(),
        'value' => new Expression('UTC_TIMESTAMP()'),
      ],
    ];
  }

  public static function tableName() {
    return 'sounds';
  }

  public function rules() {
    return [
      [['name', 'query'], 'required'],
      [['name', 'icon'], 'string'],
      [['name'], 'unique']
    ];
  }
}
