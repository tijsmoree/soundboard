<?php

use app\modules\membermodels\components\MemberDbMigration;

class m160905_200110_create_persons_advanced_view extends MemberDbMigration {
    public function up() {
        $this->db->createCommand('DROP VIEW IF EXISTS `persons_advanced`')->execute();
        $sql = file_get_contents(dirname(__FILE__) . '/m160905_200110_create_persons_advanced_view.sql');
        $this->db->createCommand($sql)->execute();
    }

    public function down() {
        $this->db->createCommand('DROP VIEW IF EXISTS `persons_advanced`')->execute();
    }
}
