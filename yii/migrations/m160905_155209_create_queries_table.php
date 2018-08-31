<?php

use yii\db\Migration;
use yii\db\mysql\Schema;

class m160905_155209_create_queries_table extends Migration {
    public function up() {
        $this->createTable('queries', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique(),
            'query' => $this->text()->notNull(),
            'created_at' => $this->dateTime()->null(),
            'updated_at' => $this->dateTime()->null()
        ]);
    }

    public function down() {
        $this->dropTable('queries');
    }
}
