<?php

use app\modules\membermodels\components\MemberDbMigration;

class m160613_161800_persons_search_fix_filtering_address_deleted_at extends MemberDbMigration {

    public function up() {
        $this->db->createCommand('DROP VIEW IF EXISTS `person_search`')->execute();
        $sql = file_get_contents(dirname(__FILE__) . '/m160613_161800_persons_search_fix_filtering_address_deleted_at.sql');
        $this->db->createCommand($sql)->execute();
    }

    public function down() {
        $this->db->createCommand('DROP VIEW IF EXISTS `person_search`')->execute();
        $sql = file_get_contents(dirname(__FILE__) . '/m160331_114500_members_persons_search_add_relevance_column.sql');
        $this->db->createCommand($sql)->execute();
    }

}
