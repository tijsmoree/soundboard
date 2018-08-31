<?php

use app\modules\membermodels\components\MemberDbMigration;

class m180325_151258_create_logs extends MemberDbMigration {

    public function up() {
        $this->createTable("logs", [
            "id" => "pk",
            "user" => "VARCHAR(255)",
            "type" => "ENUM('create', 'update', 'delete') NOT NULL",
            "model" => "VARCHAR(255) NOT NULL",
            "ids" => "VARCHAR(255) NOT NULL",
            "changes" => "TEXT",
            "created_at" => "DATETIME"
        ], "ENGINE InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    public function down() {
        $this->dropTable("logs");
    }
}