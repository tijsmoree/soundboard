<?php

use app\modules\membermodels\components\MemberDbMigration;

class m180528_091458_reversed_booleans extends MemberDbMigration {

    public function up() {
        $sql = file_get_contents(dirname(__FILE__) . '/m180528_091458_reversed_booleans.sql');
        $this->db->createCommand($sql)->execute();
    }

    public function down() {
        echo "Cannot be reverted...";
    }
}