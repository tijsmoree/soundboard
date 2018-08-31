<?php

use app\modules\membermodels\components\MemberDbMigration;

class m180314_204658_fix_titles_in_persons_advanced extends MemberDbMigration {
    public function up() {
        $sql = file_get_contents(dirname(__FILE__) . '/m180314_204658_fix_titles_in_persons_advanced.sql');
        $this->db->createCommand($sql)->execute();
    }

    public function down() {
        $sql = file_get_contents(dirname(__FILE__) . '/m161017_200856_update_persons_advanced_view.sql');
        $this->db->createCommand($sql)->execute();
    }
}