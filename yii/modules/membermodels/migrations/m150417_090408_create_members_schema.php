<?php

use app\modules\membermodels\components\MemberDbMigration;

class m150417_090408_create_members_schema extends MemberDbMigration {

    public function up() {
        $sql = file_get_contents(dirname(__FILE__) . '/m150417_090408_create_members_schema.sql');
        $this->db->createCommand($sql)->execute();
    }

    public function down() {
        echo "m150417_090408_create_members_schema cannot be reverted.\n";

        return false;
    }

}
