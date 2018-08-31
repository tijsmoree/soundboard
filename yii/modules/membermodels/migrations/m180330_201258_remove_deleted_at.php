<?php

use app\modules\membermodels\components\MemberDbMigration;
use app\modules\membermodels\models\Log;

class m180330_201258_remove_deleted_at extends MemberDbMigration {

    public function up() {
        $tables = [
            "associations" => "Association",
            "committees" => "Committee",
            "persons" => "Person",
            "person_addresses" => "PersonAddress",
            "rooms" => "Room"
        ];

        $this->dropColumn("room_access", "deleted_at");

        foreach ($tables as $table => $model) {
            $modelPath = "app\modules\membermodels\models\\" . $model;
            $models = $modelPath::find()
                ->where(['not', ['deleted_at' => null]])
                ->orderBy('deleted_at DESC')
                ->all();
            $this->dropColumn($table, "deleted_at");
            foreach ($models as $m) {
                $m->delete();
            }            
        }

        $sql = file_get_contents(dirname(__FILE__) . '/m180330_201258_remove_deleted_at_persons_advanced.sql');
        $this->db->createCommand($sql)->execute();

        $sql = file_get_contents(dirname(__FILE__) . '/m180330_201258_remove_deleted_at_persons_search.sql');
        $this->db->createCommand($sql)->execute();
    }

    public function down() {
        echo "Cannot be reverted...";
    }
}