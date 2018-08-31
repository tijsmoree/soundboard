<?php

namespace app\modules\membermodels\components;

use yii\base\Behavior;
use yii\base\Event;
use yii\db\ActiveRecord;
use app\modules\membermodels\models\Log;
use yii\base\Exception;

class LogBehavior extends Behavior {

    public $log;

    /**
     * @inheritdoc
     */
    public function attach($owner) {
        parent::attach($owner);
        
        if(!$this->owner instanceof ActiveRecord) {
            throw new Exception("This behavior is applicable only on classes that belongs or extends ActiveRecord");
        }
    }

    public function events() {
        return [
            ActiveRecord::EVENT_BEFORE_UPDATE => "logBeforeUpdate",
            ActiveRecord::EVENT_AFTER_UPDATE => "logAfterUpdate",
            ActiveRecord::EVENT_BEFORE_DELETE => "logBeforeDelete",
            ActiveRecord::EVENT_AFTER_DELETE => "logAfterDelete",
            ActiveRecord::EVENT_AFTER_INSERT => "logAfterInsert",
        ];
    }

    public function logBeforeUpdate(Event $event) {
        $this->log = new Log();

        $this->log->model = $this->owner->formName();
        $this->log->ids = $this->_getIds();
        $this->log->type = "update";
        $this->log->setChanges($this->owner);
        $this->log->save();

        return true;
    }

    public function logAfterUpdate(Event $event) {
        return $this->log->save();
    }

    public function logBeforeDelete(Event $event) {
        $this->log = new Log();

        $this->log->model = $this->owner->formName();
        $this->log->ids = $this->_getIds();
        $this->log->type = "delete";
        $this->log->setChanges($this->owner);

        return true;
    }

    public function logAfterDelete(Event $event) {
        return $this->log->save();
    }

    public function logAfterInsert(Event $event) {
        $this->log = new Log();

        $this->log->model = $this->owner->formName();
        $this->log->ids = $this->_getIds();
        $this->log->type = "create";
        $this->log->setChanges($this->owner);
        return $this->log->save();
    }

    private function _getIds() {
        $ids = [
            "id",
            "person_id",
            "association_id",
            "board_id",
            "committee_id",
            "assembly_id",
            "faculty_department_id",
            "option_id",
            "title_id",
            "type_id",
            "room_id"
        ];

        $result = [];

        foreach ($ids as $id) {
            if ($this->owner->hasAttribute($id)) {
                $result[$id] = $this->owner->$id;
            }
        }

        return json_encode($result);
    }
}