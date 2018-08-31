<?php

use app\modules\membermodels\components\MemberDbMigration;

class m161129_121248_add_committee_columns extends MemberDbMigration {
    public function up() {
        $this->addColumn('committees', 'email', 'VARCHAR(100) DEFAULT NULL AFTER long_name');
        $this->addColumn('committees', 'type', "ENUM('normal','chapter','fake') NOT NULL AFTER description");
        $this->addColumn('committees', 'has_image', 'TINYINT(1) NOT NULL DEFAULT 0 AFTER type');
        $this->execute("UPDATE committees SET `type` = 'fake' WHERE fake = 1");
        $this->dropColumn('committees', 'fake');

    }

    public function down() {
        $this->addColumn('committees', 'fake', 'TINYINT(1) NOT NULL DEFAULT 0 AFTER description');
        $this->execute("UPDATE committees SET fake = 1 WHERE `type` = 'fake'");
        $this->dropColumn('committees', 'has_image');
        $this->dropColumn('committees', 'type');
        $this->dropColumn('committees', 'email');
    }

}
