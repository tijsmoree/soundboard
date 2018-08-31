<?php

use app\modules\membermodels\components\MemberDbMigration;

class m180324_215858_fix_up_database extends MemberDbMigration {
    public function up() {
        // $this->alterColumn("associate_members", "description", "TEXT DEFAULT NULL");
        // $this->alterColumn("associations", "comments", "TEXT DEFAULT NULL");
        // $this->alterColumn("boards", "description", "TEXT DEFAULT NULL");
        $this->alterColumn("boards", "adjective", "VARCHAR(100) NOT NULL DEFAULT ''");
        $this->alterColumn("boards", "motto", "VARCHAR(200) NOT NULL DEFAULT ''");
        $this->alterColumn("board_members", "function_name", "VARCHAR(100) NOT NULL DEFAULT ''");
        $this->alterColumn("board_members", "function_number", "TINYINT(1) DEFAULT NULL");
        // $this->alterColumn("board_pictures", "description", "TEXT DEFAULT NULL");
        // $this->alterColumn("committees", "description", "TEXT DEFAULT NULL");
        $this->alterColumn("committee_members", "function_name", "VARCHAR(100) NOT NULL DEFAULT ''");
        $this->alterColumn("faculty_employments", "function", "VARCHAR(45) NOT NULL DEFAULT ''");
        // $this->alterColumn("honorary_members", "description", "TEXT DEFAULT NULL");
        // $this->alterColumn("options", "description", "TEXT DEFAULT NULL");
        $this->alterColumn("person_addresses", "address", "VARCHAR(100) NOT NULL DEFAULT ''");
        $this->alterColumn("person_addresses", "postal_code", "VARCHAR(100) NOT NULL DEFAULT ''");
        $this->alterColumn("person_addresses", "town", "VARCHAR(100) NOT NULL DEFAULT ''");
        $this->alterColumn("person_addresses", "country", "VARCHAR(100) NOT NULL DEFAULT ''");
        $this->alterColumn("person_addresses", "phone_number", "VARCHAR(100) NOT NULL DEFAULT ''");
    }

    public function down() {
        // $this->alterColumn("associate_members", "description", "TEXT");
        // $this->alterColumn("associations", "comments", "TEXT");
        // $this->alterColumn("boards", "description", "TEXT");
        $this->alterColumn("boards", "adjective", "VARCHAR(100) NOT NULL");
        $this->alterColumn("boards", "motto", "VARCHAR(200) NOT NULL");
        $this->alterColumn("board_members", "function_name", "VARCHAR(100) NOT NULL");
        $this->alterColumn("board_members", "function_number", "TINYINT(1) NOT NULL");
        // $this->alterColumn("board_pictures", "description", "TEXT");
        // $this->alterColumn("committees", "description", "TEXT");
        $this->alterColumn("committee_members", "function_name", "VARCHAR(100) NOT NULL");
        $this->alterColumn("faculty_employments", "function", "VARCHAR(45) NOT NULL");
        // $this->alterColumn("honorary_members", "description", "TEXT");
        // $this->alterColumn("options", "description", "TEXT");
        $this->alterColumn("person_addresses", "address", "VARCHAR(100) NOT NULL");
        $this->alterColumn("person_addresses", "postal_code", "VARCHAR(100) NOT NULL");
        $this->alterColumn("person_addresses", "town", "VARCHAR(100) NOT NULL");
        $this->alterColumn("person_addresses", "country", "VARCHAR(100) NOT NULL");
        $this->alterColumn("person_addresses", "phone_number", "VARCHAR(100) NOT NULL");
    }
}