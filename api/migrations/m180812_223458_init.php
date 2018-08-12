<?php

use yii\db\Migration;

class m180812_223458_init extends Migration {
  public function up() {
    $this->createTable('sounds', [
      'id' => $this->primaryKey(),
      'name' => $this->string(100)->notNull()->unique(),
      'icon' => $this->string(100)->notNull(),
      'created_at' => $this->dateTime()->null(),
      'updated_at' => $this->dateTime()->null()
    ]);
  }

  public function down() {
    $this->dropTable('sounds');
  }
}
