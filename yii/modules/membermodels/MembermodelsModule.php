<?php

namespace app\modules\membermodels;

use Yii;

/**
 * Class MembermodelsModule
 * @package app\modules\membermodels
 * @property \yii\db\Connection $db
 */
class MembermodelsModule extends \yii\base\Module {

    public $controllerNamespace = 'app\modules\membermodels\controllers';
    public $db = null;

    public function init() {
        parent::init();

        if ($this->db == null) {
            $this->db = Yii::$app->getDb();
        } else {
            $this->db = Yii::createObject($this->db);
            $this->db->open();
        }
    }

    public function getDb() {
        return $this->db;
    }

}
