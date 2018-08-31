<?php

use app\modules\membermodels\components\MemberDbMigration;

class m180305_145334_alter_faculty_tables extends MemberDbMigration {
	public function up() {
		$this->renameTable("faculty_employee_departments", "faculty_employments");
		
		$this->dropForeignKey("fk_faculty_employees_has_faculty_departments_faculty_employees1", "faculty_employments");
		$this->dropForeignKey("fk_faculty_employees_has_faculty_departments_faculty_departme1", "faculty_employments");
		$this->dropIndex("fk_faculty_employees_has_faculty_departments_faculty_employ_idx", "faculty_employments");
		$this->dropIndex("fk_faculty_employees_has_faculty_departments_faculty_depart_idx", "faculty_employments");
		$this->dropPrimaryKey("PRIMARY", "faculty_employments");
		
		$this->dropColumn("faculty_employees", "id");
		$this->dropColumn("faculty_departments", "comments");
		
		$this->renameColumn("faculty_employments", "faculty_employees_id", "person_id");
		$this->renameColumn("faculty_employments", "faculty_departments_id", "faculty_department_id");

		$this->addColumn("faculty_employments", "id", "INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST");
		$this->addColumn("faculty_employments", "installation", "DATE NULL DEFAULT NULL AFTER faculty_department_id");
		$this->addColumn("faculty_employments", "discharge", "DATE NULL DEFAULT NULL AFTER installation");
		$this->addColumn("faculty_departments", "is_support", "TINYINT(1) NOT NULL DEFAULT 0 AFTER name");
		$this->alterColumn("faculty_departments", "description", "TEXT NULL DEFAULT NULL");

		$this->addPrimaryKey("PRIMARY_employees", "faculty_employees", "person_id");

		$this->createIndex("fk_faculty_employees_has_faculty_departments_faculty_depart_idx", "faculty_employments", "faculty_department_id");
		$this->createIndex("fk_faculty_employees_has_faculty_departments_faculty_employ_idx", "faculty_employments", "person_id");

		$this->addForeignKey("fk_faculty_employees_has_faculty_departments_faculty_employees1",
			"faculty_employments",
			"person_id",
			"persons", "id",
			"RESTRICT", "RESTRICT");
		$this->addForeignKey("fk_faculty_employees_has_faculty_departments_faculty_departme1",
			"faculty_employments",
			"faculty_department_id",
			"faculty_departments", "id",
			"RESTRICT", "RESTRICT");
	}

	public function down() {
		$this->renameTable("faculty_employments", "faculty_employee_departments");

		$this->dropForeignKey("fk_faculty_employees_has_faculty_departments_faculty_employees1", "faculty_employee_departments");
		$this->dropForeignKey("fk_faculty_employees_has_faculty_departments_faculty_departme1", "faculty_employee_departments");
		$this->dropIndex("fk_faculty_employees_has_faculty_departments_faculty_depart_idx", "faculty_employee_departments");
		$this->dropIndex("fk_faculty_employees_has_faculty_departments_faculty_employ_idx", "faculty_employee_departments");
		$this->dropPrimaryKey("PRIMARY", "faculty_employees");
		$this->dropColumn("faculty_employee_departments", "id");
		$this->dropColumn("faculty_employee_departments", "installation");
		$this->dropColumn("faculty_employee_departments", "discharge");
		$this->dropColumn("faculty_employee_departments", "is_support");

		$this->addColumn("faculty_employees", "id", "INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST");
		$this->alterColumn("faculty_departments", "description", "VARCHAR(45) NOT NULL");
		$this->dropColumn("faculty_departments", "comments", "TEXT AFTER description");
		
		$this->renameColumn("faculty_employee_departments", "person_id", "faculty_employees_id");
		$this->renameColumn("faculty_employee_departments", "faculty_department_id", "faculty_departments_id");

		$this->addPrimaryKey("PRIMARY_departments", "faculty_employee_departments", "faculty_employees_id,faculty_departments_id");

		$this->createIndex("fk_faculty_employees_has_faculty_departments_faculty_depart_idx", "faculty_employee_departments", "faculty_departments_id");
		$this->createIndex("fk_faculty_employees_has_faculty_departments_faculty_employ_idx", "faculty_employee_departments", "faculty_employees_id");

		$this->addForeignKey("fk_faculty_employees_has_faculty_departments_faculty_employees1",
			"faculty_employee_departments",
			"faculty_employees_id",
			"faculty_employees", "id",
			"RESTRICT", "RESTRICT");
		$this->addForeignKey("fk_faculty_employees_has_faculty_departments_faculty_departme1",
			"faculty_employee_departments",
			"faculty_departments_id",
			"faculty_departments", "id",
			"RESTRICT", "RESTRICT");
	}
}