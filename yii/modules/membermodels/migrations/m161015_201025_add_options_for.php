<?php

use app\modules\membermodels\components\MemberDbMigration;

class m161015_201025_add_options_for extends MemberDbMigration {
    public function up() {
        $this->addColumn('options', 'for_persons', 'TINYINT(1) NOT NULL AFTER description');
        $this->addColumn('options', 'for_associations', 'TINYINT(1) NOT NULL AFTER for_persons');
    }

    public function down() {
        $this->dropColumn('options', 'for_persons');
        $this->dropColumn('options', 'for_associations');
    }
}
