<?php

use app\modules\membermodels\components\MemberDbMigration;

class m160503_141152_member_debtor_id_to_debtor_code extends MemberDbMigration {
    public function up() {
        $sql = file_get_contents(dirname(__FILE__) . '/m160503_141152_member_debtor_id_to_debtor_code.sql');
        $this->db->createCommand($sql)->execute();
    }

    public function down() {
        echo "m160503_141152_member_debtor_id_to_debtor_code cannot be reverted.\n";

        return false;
    }

}
