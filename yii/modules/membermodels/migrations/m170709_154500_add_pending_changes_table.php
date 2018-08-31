<?php

use app\modules\membermodels\components\MemberDbMigration;

class m170709_154500_add_pending_changes_table extends MemberDbMigration {
	public function up() {
		$this->createTable("pending_changes", [
			'id' => $this->primaryKey(),
			'reference_id' => $this->integer(10)->null(),
			'reference_type' => $this->string()->null(),
			'change_model_id' => $this->integer(10)->null(),
			'change_model_type' => $this->string()->notNull(),
			'change_type' => $this->string()->notNull(),
			'changes' => $this->text(),
			'file_path' => $this->text()->null(),
			'resolved_by' => $this->integer(10)->null(),
			'resolved_resolution' => "ENUM('accepted', 'rejected') default null",
			'resolved_at' => $this->dateTime()->null(),
			'created_at' => $this->dateTime()->null(),
			'updated_at' => $this->dateTime()->null()
		]);
	}

	public function down() {
		$this->dropTable("pending_changes");
	}

}
