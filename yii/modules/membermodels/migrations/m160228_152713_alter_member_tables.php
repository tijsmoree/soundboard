<?php

use app\modules\membermodels\components\MemberDbMigration;

class m160228_152713_alter_member_tables extends MemberDbMigration {

    public function up() {
        $sql = file_get_contents(dirname(__FILE__) . '/m160228_152713_alter_member_tables.sql');
        $this->db->createCommand($sql)->execute();
    }

    public function down() {
        echo "m160228_152713_alter_member_tables cannot be reverted.\n";

        return false;
    }

}
