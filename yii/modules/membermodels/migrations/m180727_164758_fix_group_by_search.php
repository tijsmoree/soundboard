<?php

use app\modules\membermodels\components\MemberDbMigration;
use app\modules\membermodels\models\Log;

class m180727_164758_fix_group_by_search extends MemberDbMigration {

    public function up() {
        $sql = file_get_contents(dirname(__FILE__) . '/m180727_164758_fix_group_by_search.sql');
        $this->db->createCommand($sql)->execute();
    }

    public function down() {
        echo "Cannot be reverted...";
    }
}