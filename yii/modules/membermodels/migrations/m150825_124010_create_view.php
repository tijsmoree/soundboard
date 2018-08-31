<?php

use app\modules\membermodels\components\MemberDbMigration;

class m150825_124010_create_view extends MemberDbMigration {

    public function up() {
        $sql = file_get_contents(dirname(__FILE__) . '/m150825_124010_create_view.sql');
        $this->db->createCommand($sql)->execute();
    }

    public function down() {
        $this->db->createCommand('DROP VIEW IF EXISTS `person_search`')->execute();
    }

}
