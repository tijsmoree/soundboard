<?php

namespace app\modules\membermodels\models;

/**
 * This is the model class for table "members.assemblies".
 *
 * @property integer $id
 * @property integer $board_id
 * @property string $type
 * @property integer $number
 * @property string $date
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Board $board
 * @property Person[] $attendees
 * @property CommitteeMember[] $dischargedMembers
 * @property CommitteeMember[] $installedMembers
 */
class Assembly extends MemberDbRecord {
    static $types = [
        'av' => 'Algemene Vergadering',
        'jv' => 'Jaarvergadering',
        'bav' => 'Buitengewone Vergadering',
        'bv' => 'Bestuursvergadering'
    ];
    
    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['board_id', 'type'], 'required'],
            [['board_id', 'number'], 'number'],
            [['type'], 'in', 'range' => array_keys(static::$types)],
            [['date'], 'date', 'format' => 'yyyy-mm-dd']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'assemblies';
    }
    
    public function getBoard() {
        return $this->hasOne(Board::className(), ['id' => 'board_id']);
    }

    public function getAttendees() {
        return $this->hasMany(Person::className(), ['id' => 'person_id'])->viaTable('assembly_attendees', ['assembly_id' => 'id']);
    }

    public function getInstalledMembers() {
        return CommitteeMember::find()
            ->where(['installation' => $this->date])
            ->all();
    }

    public function getDischargedMembers() {
        return CommitteeMember::find()
            ->where(['discharge' => $this->date])
            ->all();
    }

    public function getName() {
        $name = static::$types[$this->type] . ' ' . $this->number;

        if ($this->type == 'jv')
            $name = static::$types[$this->type];

        return $name;
    }

    public function getBoardname() {
        return $this->board->name;
    }

    private function _sortAttendeesByName($a, $b) {
        return strcmp($a->last_name, $b->last_name);
    }

    public function getListAttendees() {
        $ladies = array_filter($this->attendees, function ($i) {
            return ($i->sex == 'f');
        });
        $gentlemen = array_filter($this->attendees, function ($i) {
            return ($i->sex == 'm');
        }); 
        $other = array_filter($this->attendees, function ($i) {
            return ($i->sex == 'other');
        });

        usort($ladies, [$this, '_sortAttendeesByName']);
        usort($gentlemen, [$this, '_sortAttendeesByName']);
        usort($other, [$this, '_sortAttendeesByName']);

        $ladiesList = '';
        $gentlemenList = '';
        if (count($ladies) == 1) {
            $ladiesList .= 'de dame ';
        } elseif (count($ladies) > 1) {
            $ladiesList .= 'de dames ';
        }
        if (count($gentlemen) == 1) {
            $gentlemenList .= 'de heer ';
        } elseif (count($gentlemen) > 1) {
            $gentlemenList .= 'de heren ';
        }

        $ladiesList .= implode(', ', array_map(function ($i) {
            return $i->getNameWithTitle(false);
        }, $ladies));
        $gentlemenList .= implode(', ', array_map(function ($i) {
            return $i->getNameWithTitle(true);
        }, $gentlemen));
        $othersList = implode(', ', array_map(function ($i) {
            return $i->getNameWithTitle(false);
        }, $other));

        $attendeesList = [
            $ladiesList,
            $gentlemenList,
            $othersList,
            'het voltallige ' . $this->board->number . 'ste Bestuur der Electrotechnische Vereeniging'
        ];

        $list = implode(' en ', array_filter($attendeesList));
        $list = preg_replace("/\([^)]+\) /", "", $list);

        return ucfirst($list);
    }

    /**
     * @return array
     */
    public function getListAttributes() {
        $result = $this->getAttributes([
            'id',
            'name',
            'date'
        ]);
        return $result;
    }

    /**
     * @return array
     */
    public function getViewAttributes() {
        $result = $this->getAttributes([
            'id',
            'board_id',
            'boardname',
            'name',
            'type',
            'number',
            'date',
            'listAttendees'
        ]);

        $result['board'] = $this->board_id;

        $result['dischargedMembers'] = array_map(function ($i) {
            $res = $i->committeeViewAttributes;
            $res['committee'] = $i->committee->long_name;
            return $res;
        }, $this->dischargedMembers);
        $result['installedMembers'] = array_map(function ($i) {
            $res = $i->committeeViewAttributes;
            $res['committee'] = $i->committee->long_name;
            return $res;
        }, $this->installedMembers);
        $result['attendees'] = array_map(function ($i) {
            return $i->getAttributes([
                'id',
                'name',
                'last_name',
                'sex'
            ]);
        }, $this->attendees);

        $result['dateNice'] = date('d M Y', strtotime($this->date));
        
        return $result;
    }
}
