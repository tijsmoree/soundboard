<?php

namespace app\modules\membermodels\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "members.logs".
 *
 * @property integer $id
 * @property string $user
 * @property string $type
 * @property string $model
 * @property string $ids
 * @property string $changes
 * @property string $created_at
 */
class Log extends MemberDbRecord {
    
    private $names = [
        "Alumnus" => "alumnusschap",
        "Assembly" => "vergadering",
        "AssemblyAttendee" => "aanwezigheid",
        "AssociateMember" => "buitengewoon lidmaatschap",
        "Association" => "vereniging",
        "Board" => "Bestuur",
        "BoardMember" => "Bestuurslid",
        "BoardPicture" => "foto",
        "Committee" => "commissie",
        "CommitteeMember" => "commissielid",
        "FacultyDepartment" => "faculteitsafdeling",
        "FacultyEmployee" => "medewerkersinformatie",
        "FacultyEmployment" => "faculteitsbaan",
        "HonoraryMember" => "Erelidmaatschap",
        "NormalMember" => "inschrijving",
        "NormalMemberType" => "inschrijvingstype",
        "Option" => "optie",
        "OptionAssociationLink" => "optie",
        "OptionPersonLink" => "optie",
        "PendingChange" => "informatiewijziging",
        "Person" => "persoon",
        "PersonAddress" => "adres",
        "PersonPicture" => "foto",
        "Room" => "ruimte",
        "RoomAccess" => "ruimtetoegang",
        "Title" => "titel",
        "TitleLink" => "titel"
    ];

    /**
     * Really need this behaviors() function to override the MemberDbRecord one
     * Otherwise the LogBehavior will log creating logs as well
     * This would result in an infinite loop
     */
    public function behaviors() {
        return [
            [
                "class" => TimestampBehavior::className(),
                "attributes" => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ["created_at", false],
                    ActiveRecord::EVENT_BEFORE_UPDATE => false,
                ],
                "value" => new Expression("UTC_TIMESTAMP()"),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return "logs";
    }

    public function beforeSave($insert) {
        if (parent::beforeSave($insert) && $this->changes != "[]") {
            $this->user = $this->user ?? $_SESSION["db_access"]["name"] ?? "User";

            return true;
        } else {
            return false;
        }
    } 

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [["type", "model", "ids", "changes"], "required"],
            [["id"], "integer"],
            [["user", "type", "model", "ids", "changes"], "string"]
        ];
    }

    public function setChanges($model) {
        $except = ["created_at", "updated_at"];
        $modelAttributes = $model->getAttributes(null, $except);
        if ($this->type == "update") {
            $keys = array_diff(array_keys($model->getDirtyAttributes()), $except);

            $values = array_map(function($x) use ($model) {
                return [
                    "old" => $model->getOldAttributes()[$x],
                    "new" => $model->getDirtyAttributes()[$x]
                ];
            }, $keys);
        } elseif ($this->type == "create") {
            $keys = array_keys($modelAttributes);
            $values = array_map(function($x) use ($modelAttributes) {
                return [
                    "new" => $modelAttributes[$x]
                ];
            }, $keys);
        } elseif ($this->type == "delete") {
            $keys = array_keys($modelAttributes);
            $values = array_map(function($x) use ($modelAttributes) {
                return [
                    "old" => $modelAttributes[$x]
                ];
            }, $keys);
        } else {
            return false;
        }

        $this->changes = json_encode(array_combine($keys, $values));
        return true;
    }

    public function revert() {
        if (!$this->revertable) {
            return false;
        }

        if ($this->type == "delete") {
            $modelPath = "app\modules\membermodels\models\\" . $this->model;
            $object = new $modelPath;

            foreach ($this->getChanges() as $attribute => $value) {
                $object->$attribute = $value["old"];
            }

            return $object->save();
        } elseif ($this->type == "create") {
            return $this->getObject()->delete();
        } elseif ($this->type == "update") {
            $object = $this->getObject();

            foreach ($this->getChanges() as $attribute => $value) {
                $object->$attribute = $value["old"];
            }

            return $object->save();
        }

        return false;
    }

    public function getObject() {
        $modelPath = "app\modules\membermodels\models\\" . $this->model;

        $standardModel = new $modelPath;
        $primaryIds = $standardModel->primaryKey();
        $primaries = array_intersect_key($this->getIds(), array_flip($primaryIds));

        return $modelPath::findOne($primaries);
    }

    public function getIds() {
        $idOrder = [
            "id",
            "option_id",
            "title_id",
            "type_id",
            "room_id",
            "person_id",
            "association_id",
            "board_id",
            "committee_id",
            "assembly_id",
            "faculty_department_id"
        ];

        $ids = json_decode($this->ids, true);

        $idOrderReduced = array_intersect($idOrder, array_keys($ids));

        return array_merge(array_flip($idOrderReduced), $ids);
    }

    public function getChanges() {
        return json_decode($this->changes, true);
    }

    public function getMessage() {
        $cudMessage = [
            "create" => " is aangemaakt door ",
            "update" => " is gewijzigd door ",
            "delete" => " is verwijderd door "
        ];

        $ids = array_values($this->getIds());
        $models = array_map("static::_id2model", array_keys($this->getIds()));

        if ($this->model == "PendingChange") {
            $isResolved = $this->getChanges()["resolved_resolution"]["new"] ?? null;

            if ($isResolved == "accepted") {
                return "Een informatiewijziging is geaccepteerd door " . $this->user . ".";
            } elseif ($isResolved == "rejected") {
                return "Een informatiewijziging is geweigerd door " . $this->user . ".";
            }
        }

        if (count($ids) == 1 && !$models[0]) {
            if (static::_modelName($this->model, $ids[0])) {
                $message = $this->names[$this->model] .
                    " " .
                    static::_modelName($this->model, $ids[0]);
            } else {
                $message = "een " .
                    $this->names[$this->model];
            }
        } elseif (count($ids) == 1) {
            $message = $this->names[$this->model] .
                " van ";

            if (static::_modelName($models[0], $ids[0])) {
                $message .= $this->names[$models[0]] .
                    " " .
                    static::_modelName($models[0], $ids[0]);
            } else {
                $message .= "een " .
                    $this->names[$models[0]];
            }
        } elseif (count($ids) == 2 && !$models[0]) {
            $message = $this->names[$this->model] .
                " van ";

            if (static::_modelName($models[1], $ids[1])) {
                $message .= $this->names[$models[1]] .
                    " " .
                    static::_modelName($models[1], $ids[1]);
            } else {
                $message .= "een " .
                    $this->names[$models[1]];
            }    
        } elseif (count($ids) == 2) {
            if (static::_modelName($models[0], $ids[0])) {
                $message = $this->names[$this->model] .
                    " " .
                    static::_modelName($models[0], $ids[0]) .
                    " van ";
            } else {
                $message = "een " .
                    $this->names[$this->model] .
                    " van ";
            }
            if (static::_modelName($models[1], $ids[1])) {
                $message .= $this->names[$models[1]] .
                    " " .
                    static::_modelName($models[1], $ids[1]);
            } else {
                $message .= "een " .
                    $this->names[$models[1]];
            }
        } elseif (count($ids) == 3) {
            if (static::_modelName($models[1], $ids[1])) {
                $message = $this->names[$this->model] .
                    " " .
                    static::_modelName($models[1], $ids[1]) .
                    " van ";
            } else {
                $message = "een " .
                    $this->names[$this->model] .
                    " van ";
            }
            if (static::_modelName($models[2], $ids[2])) {
                $message .= $this->names[$models[2]] .
                    " " .
                    static::_modelName($models[2], $ids[2]);
            } else {
                $message .= "een " .
                    $this->names[$models[2]];
            }
        }

        if(isset($message)) {
            return ucfirst($message . $cudMessage[$this->type] . $this->user . ".");
        }

        return "Error: situation does not fit for a message.";
    }

    public function getIsLastChange() {
        if ($this->type == "delete" && !$this->getObject()) {
            return true;
        } elseif ($this->type != "delete" && $this->getObject()) {
            foreach ($this->getChanges() as $key => $value) {
                if ($this->getObject()[$key] != $value["new"]) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    public function getRevertable() {
        if ($this->model == "PendingChange" && isset($this->getChanges()["resolved_resolution"]["new"])) {
            return false;
        }

        if ($this->type == "delete" && $this->getObject() ||
            $this->type != "delete" && !$this->getObject()) {
            return false;
        }

        foreach (array_keys($this->getIds()) as $id) {
            $model = static::_id2model($id);
            if (!$model && $this->type == "delete") {
                continue;
            } elseif (!$model) {
                $model = $this->model;
            }
            $modelPath = "app\modules\membermodels\models\\" . $model;
            if (!$modelPath::findOne($this->getIds()[$id])) {
                return false;
            }
        }

        return true;
    }

    public function getDisplayTime() {
        // Need to change timezone, this is done using date("Z")
        return date("j-n-Y G:i", strtotime($this->created_at) + date("Z"));
    }

    public function getLinks() {
        $linkable = [
            "Person" => [
                "name" => "Persoon",
                "link" => "persons"

            ],
            "Board" => [
                "name" => "Bestuur",
                "link" => "boards"

            ],
            "Committee" => [
                "name" => "Commissie",
                "link" => "committees"

            ],
            "Association" => [
                "name" => "Vereniging",
                "link" => "associations"

            ],
            "FacultyDepartment" => [
                "name" => "Faculteitsafdeling",
                "link" => "faculty"

            ]
        ];
        $result = [];
        foreach (array_keys($this->getIds()) as $id) {
            $model = static::_id2model($id) ?? $this->model;
            if (in_array($model, array_keys($linkable))) {
                $modelPath = "app\modules\membermodels\models\\" . $model;
                if ($modelPath::findOne($this->getIds()[$id])) {
                    $result[$linkable[$model]["name"]] = "#/" . $linkable[$model]["link"] . "/" . $this->getIds()[$id];
                }
            }
        }
        return $result;
    }

    public function getSearch($query) {
        return strpos(strtolower($this->message . json_encode($this->changes)), strtolower($query)) !== false;
    }

    private static function _modelName($model, $primary) {
        $modelPath = "app\modules\membermodels\models\\" . $model;
        $modelRecord = $modelPath::findOne($primary);
        return $modelRecord->name ?? $modelRecord->short_name ?? $modelRecord->title ?? null;
    }

    private static function _id2model($idName) {
        if ($idName == "id") {
            return null;
        } elseif ($idName == "type_id") {
            return "NormalMemberType";
        }

        $model = "";
        foreach (explode("_", substr($idName, 0, -3)) as $n) {
            $model .= ucfirst($n);
        }

        return $model;
    }

    public function getViewAttributes() {
        $result = $this->getAttributes([
            "id",
            "type",
            "ids",
            "changes",
            "message",
            "links",
            "isLastChange",
            "revertable",
            "displayTime"
        ]);

        $result["ids"] = $this->getIds();
        $result["changes"] = $this->getChanges();

        return $result;
    }
}