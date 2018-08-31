<?php

use yii\db\Migration;

class m160905_150903_update_persons_search extends Migration {

  public function up() {
    $this->db->createCommand('DROP VIEW IF EXISTS `persons_search`')->execute();
    $sql = file_get_contents(dirname(__FILE__) . '/m160905_150903_update_persons_search.sql');
    $this->db->createCommand($sql)->execute();
  }

  public function down() {
    $this->db->createCommand('DROP VIEW IF EXISTS `persons_search`')->execute();
    $sql = file_get_contents(dirname(__FILE__) . '/m160613_161800_persons_search_fix_filtering_address_deleted_at.sql');
    $this->db->createCommand($sql)->execute();
  }
}
