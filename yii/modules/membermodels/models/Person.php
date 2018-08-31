<?php

namespace app\modules\membermodels\models;


use Yii;
/**
 * This is the model class for table "members.persons".
 *
 * @property integer $id
 * @property string $student_number
 * @property string $first_name
 * @property string $nickname
 * @property string $initials
 * @property string $prefix
 * @property string $last_name
 * @property string $sex
 * @property string $date_of_birth
 * @property string $date_of_death
 * @property string $email
 * @property string $mobile_phone
 * @property string $iban
 * @property string $building_access
 * @property integer $debtor_code
 * @property string $comments
 * @property string $draft
 * @property string $created_at
 * @property string $updated_at
 * @property bool $isCurrentlyMember
 * @property bool $isAlumnus
 * @property bool $isHonoraryMember
 *
 * @property Alumnus[] $alumnis
 * @property AssociateMember[] $associateMembers
 * @property BoardMember[] $boardMembers
 * @property Board[] $boards
 * @property OptionPersonLink[] $optionPersonLinks
 * @property Option[] $options
 * @property CommitteeMember[] $committeeMembers
 * @property Committee[] $committees
 * @property FacultyEmployee $facultyEmployee
 * @property FacultyEmployments[] $facultyEmployments
 * @property HonoraryMember[] $honoraryMembers
 * @property NormalMember[] $normalMembers
 * @property PersonAddress[] $personAddresses
 * @property PersonPicture[] $personPictures
 * @property RoomAccess[] $roomAccesses
 * @property Room[] $rooms
 * @property Title[] $titles
 * @property TitleLink[] $titleLinks
 * @property string $formOfAddress
 * @property string $salutation
 * @property string $name
 * @property string $formalName
 * @property string $nameWithTitle
 * @property string $lastNameWithPrefix
 * @property string $formalNameWithTitle
 * @property array $apiInfo
 * @property PersonAddress $homeAddress
 * @property PersonAddress $parentAddress
 */
class Person extends MemberDbRecord {

    public $count, $birthday;

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'student_number' => Yii::t('app', 'Studie nummer'),
            'first_name' => Yii::t('app', 'Voornaam'),
            'nickname' => Yii::t('app', 'Roepnaam'),
            'initials' => Yii::t('app', 'Initialen'),
            'prefix' => Yii::t('app', 'Prefix'),
            'last_name' => Yii::t('app', 'Achternaam'),
            'sex' => Yii::t('app', 'Geslacht'),
            'date_of_birth' => Yii::t('app', 'Geboorte datum'),
            'date_of_death' => Yii::t('app', 'Sterf datum'),
            'mobile_phone' => Yii::t('app', 'Mobiel nummer'),
            'iban' => Yii::t('app', 'IBAN rekening nummer'),
            'building_access' => Yii::t('app', 'Gebouw toegang'),
            'debtor_code' => Yii::t('app', 'Debiteur code'),
            'comments' => Yii::t('app', 'Commentaar'),
            'draft' => Yii::t('app', 'Draft'),
            'created_at' => Yii::t('app', 'Aangemaakt op'),
            'updated_at' => Yii::t('app', 'Bijgewerkt op'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'persons';
    }

    public function beforeSave($insert) {
        $this->comments = $this->comments ?? "";

        return parent::beforeSave($insert);
    }

    public static function findByStudentNumber($student_number) {
        return static::findOne(['student_number' => $student_number]);
    }

    public static function getBirthdays($daysForward = 7, $daysBack = 2, $currentTime = null) {
        /*
         * Before changing this, think about leap years ;)
         * Really, do it. It's painfull
         * No, this does not work: http://stackoverflow.com/questions/9703367/mysql-dayofyear-in-leap-year
         * Neither this: http://stackoverflow.com/questions/1490488/how-to-find-the-birthday-of-friends-who-are-celebrating-today-using-php-and-mysq
         */
        if ($currentTime === null) {
            $currentTime = time();
        }
        $beginTime = $currentTime - ($daysBack * 24 * 3600);
        $beginDay = date('j', $beginTime);
        $beginMonth = date('n', $beginTime);
        $endTime = $currentTime + ($daysForward * 24 * 3600);
        $endDay = date('j', $endTime);
        $endMonth = date('n', $endTime);
        $dateFilter = '';
        if ($beginMonth === $endMonth) {
            $dateFilter = "(
                month(date_of_birth) = '${beginMonth}' AND
                dayofmonth(date_of_birth) >= '${beginDay}' AND
                dayofmonth(date_of_birth) <= '${endDay}'
            )";
        } else {
            $dateFilter = "(
                (
                    month(date_of_birth) = '${beginMonth}' AND
                    dayofmonth(date_of_birth) >= '${beginDay}'
                )
                OR
               (
                    month(date_of_birth) = '${endMonth}' AND
                    dayofmonth(date_of_birth) <= '${endDay}'
                )
            ";
            if ($beginMonth > $endMonth) {
                /*
                 * Selecting from 2016-11-01 until 2017-02-01
                 * This covers months 12, 1
                 */
                $dateFilter .= " OR (
                    month(date_of_birth) > '${beginMonth}' OR
                    month(date_of_birth) < '${endMonth}'
                ))";
            } else {
                /*
                 * Selecting from 2016-05-01 until 2016-09-01
                 * This covers months 6,7,8
                 */
                $dateFilter .= " OR (
                    month(date_of_birth) > '${beginMonth}' AND
                    month(date_of_birth) < '${endMonth}'
                ))";
            }
        }
        $birthdays = static::find()
            ->select([static::tableName() . '.*'])
            ->joinWith('normalMembers')
            ->andWhere($dateFilter)
            ->andWhere('registration IS NOT NULL AND deregistration IS NULL')
//            ->orderBy(['birthday' => 'DESC'])
            ->all();

        foreach($birthdays as $person) {
            /*
             * Correct birthdays when we select during a year change
             * from 30-12-2016 until 1-1-2017, should return dates in the correct years
             */
            $birthday = strtotime($person->date_of_birth);
            $person->birthday = date('Y', $beginTime) . date('-m-d', $birthday);
            if (strtotime($person->birthday) < strtotime(date('Y-m-d', $beginTime))) {
                $person->birthday = date('Y', $endTime) . date('-m-d', $birthday);
            }
        }
        usort($birthdays, function($a, $b) {
            return $a->birthday > $b->birthday ? 1 : -1;
        });
        return $birthdays;
    }


    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['last_name'], 'required'],
            [['first_name', 'nickname', 'initials', 'prefix', 'last_name', 'email', 'mobile_phone', 'iban', 'comments', 'draft'], 'string'],
            [['sex'], 'in', 'range' => ['m', 'f', 'other']],
            [['building_access'], 'in', 'range' => ['normal', 'weekend', 'always']],
            [['date_of_birth', 'date_of_death'], 'date', 'format' => 'yyyy-mm-dd'],
            [['student_number', 'debtor_code'], 'number'],
            [['student_number'], 'unique'],
            [['initials'], 'filter', 'filter' => function ($value) {
                if (!preg_match('/^([A-Z][A-Za-z]?\.)+$/', $value)) {
                    return preg_replace('/(.)/', '$1.', strtoupper(preg_replace("/[^A-Za-z]/", '', $value)));
                } else {
                    return $value;
                }
            }],
            [['email'], 'match', 'pattern' => '/^.+@.+\..+$/', 'message' => 'Het mailadres is niet correct.'],
            [['mobile_phone'], 'match', 'pattern' => '/^\+( ?\d){6,15}$/', 'message' => 'Het telefoonnummer is niet correct. Het moet in internationale notatie zijn.'],
            [['iban'], 'match', 'pattern' => '/^[A-Z]{2} ?[0-9]{2} ?[a-zA-Z0-9]{4} ?([0-9] ?){7}([a-zA-Z0-9]? ?){0,16}$/', 'message' => 'Het IBAN is niet correct.']
        ];
    }

    /**
     * @return string
     */
    public function getName() {
        $firstName = ($this->first_name == '') ? $this->initials : $this->first_name;
        return trim(trim($firstName . ($this->nickname ? ' (' . $this->nickname . ')' : '') . ' ' . $this->prefix) . ' ' . $this->last_name);
    }

    public function getQueryName() {
    	$firstName = ($this->first_name == '') ? $this->initials : $this->first_name;
        return trim(trim($firstName . ' ' . $this->prefix) . ' ' . $this->last_name);
    }

    public function getFormalName() {
        return trim(trim($this->initials . ' ' . $this->prefix) . ' ' . $this->last_name);
    }

    public function getLastNameWithPrefix() {
        return trim(ucfirst($this->prefix) . ' ' . $this->last_name);
    }

    private $_orderedTitles;

    private function _orderTitles() {
        $this->_orderedTitles = $this->titles;

        usort($this->_orderedTitles, function ($a, $b) {
            if ($a->rank == $b->rank) {
                return 0;
            }
            return ($a->rank < $b->rank) ? -1 : 1;
        });
    }

    public function getTitle() {
        if ($this->_orderedTitles == null) {
            $this->_orderTitles();
        }

        $name = '';
        foreach ($this->_orderedTitles as $title) {
            if ($title->front) {
                $name .= strtolower($title->title) . ' ';
            }
        }
        $name = ucfirst($name);
        foreach ($this->_orderedTitles as $title) {
            if (!$title->front) {
                $name .= $title->title . ' ';
            }
        }

        return $name;
    }

    public function getNameWithTitle($formal = false) {
        if ($this->_orderedTitles == null) {
            $this->_orderTitles();
        }

        $name = '';
        foreach ($this->_orderedTitles as $title) {
            if ($title->front) {
                $name .= strtolower($title->title) . ' ';
            }
        }
        $name = ucfirst($name);
        if ($formal) {
            $name .= $this->formalName;
        } else {
            $name .= $this->name;
        }
        foreach ($this->_orderedTitles as $title) {
            if (!$title->front) {
                $name .= ' ' . $title->title;
            }
        }

        return $name;
    }

    public function getFormalNameWithTitle() {
        return $this->getNameWithTitle(true);
    }

    public function getFormOfAddress() {
        if ($this->_orderedTitles == null) {
            $this->_orderTitles();
        }

        if (count($this->_orderedTitles) > 0) {
            $address = $this->_orderedTitles[0]->form_of_address;
        } else {
            $address = 'De weledelgeboren';
        }

        switch ($this->sex) {
            case 'm':
                $address .= ' heer';
                break;
            case 'f':
                $address .= ' mevrouw';
                break;
        }

        return $address;
    }

    public function getSalutation() {
        $salutation = "Geachte";
        switch ($this->sex) {
            case 'm':
                $salutation .= ' heer';
                break;
            case 'f':
                $salutation .= ' mevrouw';
                break;
        }
        return $salutation;
    }

    public function getIsCurrentlyMember() {
        return $this->_is('normalMembers');
    }

    public function getIsAlumnus() {
        return $this->_is('alumnis');
    }

    public function getIsHonoraryMember() {
        return $this->_is('honoraryMembers');
    }

    private function _is($relationName) {
        $relation = $this->$relationName;
        $field = ($relationName == 'honoraryMembers') ? 'discharge' : 'deregistration';
        foreach ($relation as $item) {
            if ($item->$field === null) {
                return true;
                break;
            }
        }
        return false;
    }

    private $_enrollments = null;

    public function getEnrollments() {
        if ($this->_enrollments === null) {
            $all = [
                'alumnus' => $this->alumnis, //registration - deregistration
                'associate_member' => $this->associateMembers, //registration - deregistration
                'honorary_member' => $this->honoraryMembers, //installation - discharge
                'normal_member' => $this->normalMembers //registration - deregistration
            ];

            $this->_enrollments = [];
            foreach ($all as $type => $items) {
                if (empty($items)) {
                    continue;
                }

                foreach ($items as $item) {
                    $this->_enrollments[] = [
                        'type' => $type,
                        'registration' => ($type == 'honorary_member') ? $item->installation : $item->registration,
                        'deregistration' => ($type == 'honorary_member') ? $item->discharge : $item->deregistration,
                        'period' => $item->period,
                        'item' => $item->viewAttributes
                    ];
                }
            }
        }

        return $this->_enrollments;
    }

    /**
     * @return true
     */
    public function getPendingChanges() {
        $changes = PendingChange::find()
            ->where(['reference_type' => 'Person'])
            ->andWhere(['reference_id' => $this->id])
            ->andWhere(['resolved_resolution' => NULL])
            ->count();

        return ($changes > 0);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlumnis() {
        return $this->hasMany(Alumnus::className(), ['person_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAssociateMembers() {
        return $this->hasMany(AssociateMember::className(), ['person_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBoardMembers() {
        return $this->hasMany(BoardMember::className(), ['person_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBoards() {
        return $this->hasMany(Board::className(), ['id' => 'board_id'])->viaTable('board_members', ['person_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOptionPersonLinks() {
        return $this->hasMany(OptionPersonLink::className(), ['person_id' => 'id']);
    }

    /*
     * Legacy
     */
    public function getCheckboxes() {
        return $this->getOptions();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOptions() {
        return $this->hasMany(Option::className(), ['id' => 'option_id'])->viaTable('option_person_links', ['person_id' => 'id'])->orderBy('name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommitteeMembers() {
        return $this->hasMany(CommitteeMember::className(), ['person_id' => 'id'])->joinWith('committee');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommittees() {
        return $this->hasMany(Committee::className(), ['id' => 'committee_id'])->viaTable('committee_members', ['person_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFacultyEmployee() {
        return $this->hasOne(FacultyEmployee::className(), ['person_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFacultyEmployments() {
        return $this->hasMany(FacultyEmployment::className(), ['person_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHonoraryMembers() {
        return $this->hasMany(HonoraryMember::className(), ['person_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNormalMembers() {
        return $this->hasMany(NormalMember::className(), ['person_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPersonAddresses() {
        return $this->hasMany(PersonAddress::className(), ['person_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPictures() {
        return $this->hasMany(PersonPicture::className(), ['person_id' => 'id'])->orderBy('created_at');
    }

    public function getMainPicture() {
        return $this->getPictures()->where(['main' => 1])->orderBy('updated_at DESC')->one();
    }

    public function getPictureUrl() {
        $picture = $this->getPictures()->where(['main' => 1])->orderBy('updated_at DESC')->one();
        if ($picture === null) {
            return null;
        }

        return $picture->url;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoomAccess() {
        return $this->hasMany(RoomAccess::className(), ['person_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRooms() {
        return $this->hasMany(Room::className(), ['id' => 'room_id'])->via('roomAccess');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTitles() {
        return $this->hasMany(Title::className(), ['id' => 'title_id'])->viaTable('title_person_links', ['person_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTitleLinks() {
        return $this->hasMany(TitleLink::className(), ['person_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddresses() {
        return $this->hasMany(PersonAddress::className(), ['person_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHomeAddress() {
        return $this->hasOne(PersonAddress::className(), ['person_id' => 'id'])
            ->andWhere(['type' => 'home']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParentAddress() {
        return $this->hasOne(PersonAddress::className(), ['person_id' => 'id'])
            ->andWhere(['type' => 'parents']);
    }

    /**
     * @return array
     */
    public function getApiInfo() {
        $result = $this->getAttributes([
            'id',
            'name',
            'nameWithTitle',
            'date_of_birth',
            'email',
            'mobile_phone',
            'pictureUrl'
        ]);

        $result['homeAddress'] = ($this->homeAddress === null ? null : $this->homeAddress->apiInfo);
        $result['parentAddress'] = ($this->parentAddress === null ? null : $this->parentAddress->apiInfo);

        $result['committees'] = array_map(function (CommitteeMember $committeeMember) {
            return $committeeMember->apiInfo;
        }, $this->committeeMembers);

        return $result;
    }

    public function delete() {
        foreach ($this->alumnis as $link) {
            if($link) {
                $link->delete();
            }
        }
        foreach ($this->associateMembers as $link) {
            if($link) {
                $link->delete();
            }
        }
        foreach ($this->boardMembers as $link) {
            if($link) {
                $link->delete();
            }
        }
        foreach ($this->optionPersonLinks as $link) {
            if($link) {
                $link->delete();
            }
        }
        foreach ($this->committeeMembers as $link) {
            if($link) {
                $link->delete();
            }
        }
        if ($this->facultyEmployee) {
            $this->facultyEmployee->delete();
        }
        foreach ($this->facultyEmployments as $link) {
            if($link) {
                $link->delete();
            }
        }
        foreach ($this->honoraryMembers as $link) {
            if($link) {
                $link->delete();
            }
        }
        foreach ($this->normalMembers as $link) {
            if($link) {
                $link->delete();
            }
        }
        foreach ($this->personAddresses as $link) {
            if($link) {
                $link->delete();
            }
        }
        foreach ($this->roomAccess as $link) {
            if($link) {
                $link->delete();
            }
        }
        foreach ($this->titleLinks as $link) {
            if($link) {
                $link->delete();
            }
        }
        foreach ($this->pictures as $link) {
            if($link) {
                $link->delete();
            }
        }

        return parent::delete();
    }
}
