<?php

use app\modules\membermodels\components\MemberDbMigration;

class m180314_201858_fix_new_sql_version extends MemberDbMigration {
	public function up() {
		$this->alterColumn("committee_members", "function_name", "VARCHAR(100) DEFAULT NULL");

		$this->alterColumn("person_addresses", "address", "VARCHAR(100) DEFAULT NULL");
		$this->alterColumn("person_addresses", "postal_code", "VARCHAR(100) DEFAULT NULL");
		$this->alterColumn("person_addresses", "town", "VARCHAR(100) DEFAULT NULL");
		$this->alterColumn("person_addresses", "country", "VARCHAR(100) DEFAULT NULL");
		$this->alterColumn("person_addresses", "phone_number", "VARCHAR(100) DEFAULT NULL");

		$this->createIndex("deregistration", "normal_members", "deregistration");

		$this->alterColumn("persons", "nickname", "VARCHAR(100) DEFAULT NULL");
		$this->alterColumn("persons", "email", "VARCHAR(100) DEFAULT NULL");
		$this->alterColumn("persons", "mobile_phone", "VARCHAR(45) DEFAULT NULL");
		$this->alterColumn("persons", "iban", "VARCHAR(45) DEFAULT NULL");
		$this->alterColumn("persons", "comments", "TEXT");
		$this->createIndex("debtor_code", "persons", "debtor_code");
		$this->createIndex("deleted_at", "persons", "deleted_at");
	}

	public function down() {
		$this->alterColumn("committee_members", "function_name", "VARCHAR(100) NOT NULL");

		$this->alterColumn("person_addresses", "address", "VARCHAR(100) NOT NULL");
		$this->alterColumn("person_addresses", "postal_code", "VARCHAR(100) NOT NULL");
		$this->alterColumn("person_addresses", "town", "VARCHAR(100) NOT NULL");
		$this->alterColumn("person_addresses", "country", "VARCHAR(100) NOT NULL");
		$this->alterColumn("person_addresses", "phone_number", "VARCHAR(100) NOT NULL");

		$this->dropIndex("deregistration", "normal_members");

		$this->alterColumn("persons", "nickname", "VARCHAR(100) NOT NULL DEFAULT ''");
		$this->alterColumn("persons", "email", "VARCHAR(100) NOT NULL DEFAULT ''");
		$this->alterColumn("persons", "mobile_phone", "VARCHAR(45) NOT NULL DEFAULT ''");
		$this->alterColumn("persons", "iban", "VARCHAR(45) NOT NULL DEFAULT ''");
		$this->alterColumn("persons", "comments", "TEXT NOT NULL");
		$this->dropIndex("debtor_code", "persons", "debtor_code");
		$this->dropIndex("deleted_at", "persons", "deleted_at");
	}
}