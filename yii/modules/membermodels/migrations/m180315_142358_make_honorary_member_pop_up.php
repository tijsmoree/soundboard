<?php

use app\modules\membermodels\components\MemberDbMigration;

class m180315_142358_make_honorary_member_pop_up extends MemberDbMigration {
    public function up() {
        $sql = file_get_contents(dirname(__FILE__) . '/m180315_142358_make_honorary_member_pop_up.sql');
        $this->db->createCommand($sql)->execute();
    }

    public function down() {
        $sql = file_get_contents(dirname(__FILE__) . '/m160905_150903_update_persons_search.sql');
        $this->db->createCommand($sql)->execute();
    }
}