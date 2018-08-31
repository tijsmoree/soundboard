<?php

use yii\db\Migration;

class m180324_182234_add_standard_queries extends Migration {

    public function up() {
        $timestamp = date('Y-m-d h:i:s');
        $committee = "SELECT p.full_name, c.function_name, c.installation\n" .
            "FROM committee_members c\n" .
            "INNER JOIN persons_advanced p ON p.id = c.person_id\n" .
            "WHERE c.committee_id = __ID__ AND c.discharge IS NULL\n" .
            "ORDER BY c.function_number";
        $board = "SELECT p.full_name, b.function_name\n" .
            "FROM board_members b\n" .
            "INNER JOIN persons_advanced p ON p.id = b.person_id\n" .
            "WHERE b.board_id = __ID__\n" .
            "ORDER BY b.function_number";
        $faculty_department = "SELECT p.full_name, f.function\n" .
            "FROM faculty_employments f\n" .
            "INNER JOIN persons_advanced p ON p.id = f.person_id\n" .
            "WHERE f.faculty_department_id = __ID__ AND f.discharge IS NULL";

        $this->insert('queries', [
            'id' => 1001,
            'name' => 'Commissie',
            'group' => 'Standaard',
            'query' => $committee,
            'created_at' => $timestamp,
            'updated_at' => $timestamp
        ]);
        $this->insert('queries', [
            'id' => 1002,
            'name' => 'Bestuur',
            'group' => 'Standaard',
            'query' => $board,
            'created_at' => $timestamp,
            'updated_at' => $timestamp
        ]);
        $this->insert('queries', [
            'id' => 1003,
            'name' => 'Faculteitsafdeling',
            'group' => 'Standaard',
            'query' => $faculty_department,
            'created_at' => $timestamp,
            'updated_at' => $timestamp
        ]);
    }

    public function down() {
        $this->delete('queries', '`group` = \'Standaard\'');
    }
}
