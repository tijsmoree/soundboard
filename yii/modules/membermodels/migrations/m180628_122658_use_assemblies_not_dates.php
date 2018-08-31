<?php

use app\modules\membermodels\components\MemberDbMigration;

class m180628_122658_use_assemblies_not_dates extends MemberDbMigration {

    public function up() {
        $this->createTable("assemblies", [
            "id" => "pk",
            "board_id" => "INT(11) NOT NULL",
            "type" => "ENUM('av', 'jv', 'bav', 'bv') NOT NULL",
            "number" => "TINYINT(1)",
            "date" => "DATE",
            "created_at" => "DATETIME NOT NULL",
            "updated_at" => "DATETIME NOT NULL"
        ], "ENGINE InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $this->addForeignKey("assembly_has_board",
            "assemblies",
            "board_id",
            "boards", "id",
            "RESTRICT", "RESTRICT");

        $this->createTable("assembly_attendees", [
            "assembly_id" => "INT(11) NOT NULL",
            "person_id" => "INT(11) NOT NULL",
            "created_at" => "DATETIME NOT NULL",
            "updated_at" => "DATETIME NOT NULL"
        ], "ENGINE InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $this->addPrimaryKey("PRIMARY_attendees", "assembly_attendees", ["assembly_id", "person_id"]);
        $this->addForeignKey("attendee_has_assembly",
            "assembly_attendees",
            "assembly_id",
            "assemblies", "id",
            "RESTRICT", "RESTRICT");
        $this->addForeignKey("attendee_has_person",
            "assembly_attendees",
            "person_id",
            "persons", "id",
            "RESTRICT", "RESTRICT");
    }

    public function down() {
        echo "Cannot be reverted...";
    }
}