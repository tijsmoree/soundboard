<?php

namespace app\modules\membermodels\models;

use yii\helpers\Url;

/**
 * This is the model class for table "members.committees".
 *
 * @property integer $id
 * @property string $short_name
 * @property string $long_name
 * @property string $email
 * @property string $description
 * @property string $type
 * @property boolean $fake
 * @property boolean $has_image
 * @property string $created_at
 * @property string $updated_at
 * @property string $imageFile
 * @property string $imageUrl
 * @property string $imageUrlBustingCache
 *
 * @property CommitteeMember[] $committeeMembers
 * @property Person[] $people
 */
class Committee extends MemberDbRecord {
    static $typeOptions = ['normal' => 'Normal committee', 'chapter' => 'Chapter (Dispuut)', 'fake' => 'No committee, only for administration'];
    protected static $maxPictureSize = 2048;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'committees';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return [
            [['short_name', 'long_name'], 'required'],
            [['short_name', 'long_name', 'description'], 'string'],
            ['email', 'email'],
            [['type'], 'in', 'range' => array_keys(static::$typeOptions)]
        ];
    }

    public function beforeSave($insert) {
        $this->description = $this->description ?? "";

        return parent::beforeSave($insert);
    } 

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommitteeMembers() {
        return $this->hasMany(CommitteeMember::className(), ['committee_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActiveCommitteeMembers() {
        return $this->hasMany(CommitteeMember::className(), ['committee_id' => 'id'])
            ->where('discharge IS NULL');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPeople() {
        return $this->hasMany(Person::className(), ['id' => 'person_id'])->viaTable('committee_members', ['committee_id' => 'id']);
    }

    public function getHasActiveCommitteeMembers() {
        return (count($this->activeCommitteeMembers) > 0);
    }

    public function getLastInstallation() {
        if (count($this->committeeMembers) == 0) {
            return null;
        }

        $lastInstallation = null;
        foreach ($this->committeeMembers as $member) {
            $installation = strtotime($member->installation);
            if ($lastInstallation == null || $installation > $lastInstallation) {
                $lastInstallation = $installation;
            }
        }
        return $lastInstallation;
    }

    /**
     * @return null|string
     */
    public function getImageUrl() {
    	// Don't return null, is responsibility of the controller.
        return Url::to(['/members/committee/image', 'id' => $this->id]);
    }

    public function getImageUrlBustingCache() {
        if (!$this->has_image) {
            return null;
        }
        return $this->imageUrl . '&time=' . time();
    }

    /**
     * @return string
     */
    public function getImageFile() {
        if (!$this->has_image) {
            return null;
        }
        if (!defined('IMAGE_DIR')) {
            define('IMAGE_DIR', '/mnt/web_content/ledendb');
        }
        return IMAGE_DIR . "/committees/{$this->id}.jpg";
    }

    /**
     * @return boolean
     */
    public function getFake() {
        return ($this->type == 'fake');
    }

    public function removeImage() {
        if (!$this->has_image) {
            return false;
        }

        @unlink($this->imageFile);
        $this->has_image = 0;
        return $this->save();
    }

    /**
     * @return array
     */
    public function getApiInfo() {
        $result = $this->getAttributes([
            'id',
            'short_name',
            'long_name',
            'description'
        ]);
        return $result;
    }

    /**
     * @return array
     */
    public function getListAttributes() {
        $result = $this->getAttributes([
            'id',
            'short_name',
            'long_name',
            'type',
            'activeCommitteeMembers',
            'hasActiveCommitteeMembers',
            'lastInstallation'
        ]);
        return $result;
    }

    public function getViewAttributes() {
        $result = $this->getAttributes([
            'id',
            'short_name',
            'long_name',
            'email',
            'description',
            'type',
            'has_image',
            'imageUrlBustingCache'
        ]);
        $result['committeeMembers'] = array_map(function ($i) {
            return $i->committeeViewAttributes;
        }, $this->committeeMembers);

        return $result;
    }

    public function saveImage($file) {
        $this->has_image = 1;

        if (!move_uploaded_file($file['tmp_name'] , $this->imageFile)) {
            return false;
        }
        $this->save();

        $image = new \Eventviva\ImageResize($this->imageFile);
        $image->resizeToBestFit(static::$maxPictureSize, static::$maxPictureSize);
        $image->save($this->imageFile);

        return $this->save();
    }

    public function delete() {
        foreach ($this->committeeMembers as $link) {
            $link->delete();
        }

        return parent::delete();
    }
}
