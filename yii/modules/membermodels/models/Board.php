<?php

namespace app\modules\membermodels\models;

/**
 * This is the model class for table "members.boards".
 *
 * @property integer $id
 * @property integer $number
 * @property string $adjective
 * @property string $motto
 * @property string $color
 * @property integer $lustrum
 * @property string $installation
 * @property string $installation_precision
 * @property string $discharge
 * @property string $discharge_precision
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 *
 * @property BoardMember[] $boardMembers
 * @property Person[] $people
 * @property BoardPicture[] $pictures
 */
class Board extends MemberDbRecord {
    static $precisions = [
        'day', 'month', 'year'
    ];

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['adjective'], 'string', 'max' => 100],
            [['motto'], 'string', 'max' => 200],
            [['color', 'description'], 'string'],
            ['lustrum', 'boolean'],
            [['installation', 'discharge'], 'date', 'format' => 'yyyy-mm-dd']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'boards';
    }

    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            $this->installation_precision = (strpos($this->installation, '-01-01') !== false) ? 'year' : 'day';
            $this->discharge_precision = (strpos($this->discharge, '-01-01') !== false) ? 'year' : 'day';
            $this->description = $this->description ?? '';
            return true;
        } else {
            return false;
        }
    }

    public function getName() {
        $name = ($this->lustrum) ? 'Lustrumbestuur' : 'Bestuur';
        $name .= " ";
        if ($this->number !== null) {
            $name .= $this->number;
        } else {
            $name .= $this->getPeriod('year');
        }

        return $name;
    }
    
    public function getPeriod($precision = null) {
        $start = '';
        $end = '';

        if ($precision !== null && !in_array($precision, static::$precisions)) {
            $precision = null;
        } else {
            $precision = array_search($precision, static::$precisions);
        }

        if ($this->installation == null) {
            $start = 'Onbekend';
        } else {
            $installation = strtotime($this->installation);
            switch (max(array_search($this->installation_precision, static::$precisions), $precision)) {
                case 0:
                    $start = date('d M Y', $installation);
                    break;
                case 1:
                    $start = date('M Y', $installation);
                    break;
                case 2:
                    $start = date('Y', $installation);
                    break;
            }
        }

        if ($this->discharge == null) {
            $end = 'Heden';
        } else {
            $discharge = strtotime($this->discharge);
            switch (max(array_search($this->discharge_precision, static::$precisions), $precision)) {
                case 0:
                    $end = date('d M Y', $discharge);
                    break;
                case 1:
                    $end = date('M Y', $discharge);
                    break;
                case 2:
                    $end = date('Y', $discharge);
                    break;
            }
        }

        if ($start == '' && $end == '') {
            $period = '';
        } else if ($start == $end) {
            $period = $start;
        } else {
            $period = $start . ' - ' . $end;
        }
        
        return $period;
    }
    
    public function getPeriodInYears() {
        return $this->getPeriod('year');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBoardMembers() {
        return $this->hasMany(BoardMember::className(), ['board_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPictures() {
        return $this->hasMany(BoardPicture::className(), ['board_id' => 'id'])->orderBy('priority ASC');
    }

    public function getMainPicture() {
        return $this->getPictures()->orderBy('priority DESC')->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeople() {
        return $this->hasMany(Person::className(), ['id' => 'person_id'])->via('boardMembers');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAssemblies() {
        return $this->hasMany(Assembly::className(), ['board_id' => 'id'])->orderBy('date DESC');
    }

    /**
     * @return array
     */
    public function getListAttributes() {
        $result = $this->getAttributes([
            'id',
            'name',
            'periodInYears',
            'color'
        ]);
        return $result;
    }

    /**
     * @return array
     */
    public function getViewAttributes() {
        $result = $this->getAttributes([
            'id',
            'name',
            'number',
            'adjective',
            'installation',
            'discharge',
            'motto',
            'color',
            'lustrum',
            'description',
            'period',
            'periodInYears',
            'pictures'
        ]);
        $result['boardMembers'] = array_map(function ($i) {
            return $i->boardViewAttributes;
        }, $this->boardMembers);
        $result['pictures'] = array_map(function ($i) {
            return $i->getAttributes([
                'id',
                'priority',
                'description',
                'url'
            ]);
        }, $this->pictures);

        return $result;
    }
}
