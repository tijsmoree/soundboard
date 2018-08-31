<?php

use app\modules\membermodels\components\MemberDbMigration;

class m160228_161224_alter_members_persons_search extends MemberDbMigration {

    public function up() {
        $this->db->createCommand('DROP VIEW IF EXISTS `person_search`')->execute();
        $sql = file_get_contents(dirname(__FILE__) . '/m160228_161224_alter_members_persons_search.sql');
        $this->db->createCommand($sql)->execute();
        
    }

    public function down() {
        $this->db->createCommand('DROP VIEW IF EXISTS `person_search`')->execute();
        $sql = file_get_contents(dirname(__FILE__) . '/m150825_124010_create_view.sql');
        $this->db->createCommand($sql)->execute();
    }

    /*
      // Use safeUp/safeDown to run migration code within a transaction
      public function safeUp()
      {
      }

      public function safeDown()
      {
      }
     */
}
