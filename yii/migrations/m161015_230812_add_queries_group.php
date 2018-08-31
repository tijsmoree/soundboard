<?php

use yii\db\Migration;

class m161015_230812_add_queries_group extends Migration {
    public function up() {
        $this->addColumn('queries', 'group', 'VARCHAR(255) AFTER name');
    }

    public function down() {
        $this->dropColumn('queries', 'group');
    }
}
