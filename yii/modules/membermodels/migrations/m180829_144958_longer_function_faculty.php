<?php

use app\modules\membermodels\components\MemberDbMigration;

class m180829_144958_longer_function_faculty extends MemberDbMigration {

    public function up() {
        $this->alterColumn('faculty_employments', 'function', $this->string(100)->notNull());
    }

    public function down() {
        $this->alterColumn('faculty_employments', 'function', $this->string(45)->notNull());
    }
}