<?php

use app\modules\membermodels\components\MemberDbMigration;

class m161125_165102_remove_person_pictures_taken_at extends MemberDbMigration {
    public function up() {
        $this->dropColumn('person_pictures', 'taken_at');
    }

    public function down() {
        $this->addColumn('person_pictures', 'taken_at', 'DATE AFTER file_name');
    }
}
