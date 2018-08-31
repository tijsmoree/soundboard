<?php

use app\modules\membermodels\components\MemberDbMigration;

class m160331_114500_members_persons_search_add_relevance_column extends MemberDbMigration {

    public function up() {
        $this->db->createCommand('DROP VIEW IF EXISTS `person_search`')->execute();
        $sql = file_get_contents(dirname(__FILE__) . '/m160331_114500_members_persons_search_add_relevance_column.sql');
        $this->db->createCommand($sql)->execute();
    }

    public function down() {
        $this->db->createCommand('DROP VIEW IF EXISTS `person_search`')->execute();
        $sql = file_get_contents(dirname(__FILE__) . '/m160228_161224_alter_members_persons_search.sql');
        $this->db->createCommand($sql)->execute();
    }

}
