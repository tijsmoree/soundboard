<?php

namespace app\modules\membermodels\components;

use Yii;

/**
 * Created by PhpStorm.
 * User: Paul Marcelis
 * Date: 18-5-2016
 * Time: 16:02
 */
class MemberDbMigration extends \yii\db\Migration {

    public function init() {
        parent::init();
        $this->db = Yii::$app->getModule('membermodels')->db;
    }
}
