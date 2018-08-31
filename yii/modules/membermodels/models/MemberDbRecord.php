<?php

namespace app\modules\membermodels\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use app\modules\membermodels\components\LogBehavior;

/**
 * Description of MemberDbRecord
 *
 * @author Paul Marcelis
 */
class MemberDbRecord extends ActiveRecord {

    public function behaviors() {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('UTC_TIMESTAMP()'),
            ],
            'log' => [
                'class' => LogBehavior::className()
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getDb() {
        return Yii::$app->getModule('membermodels')->getDb();
    }

    public static function getDbName() {
        return static::_getDsnAttribute('dbname', static::getDb());
    }

    /**
     * Additional function to get the table name with the database name prefixed
     *
     * @return string
     */
    public static function absTableName() {
        $dbName = static::getDbName();
        $tableName = static::tableName();

        return "`{$dbName}`.{$tableName}";
    }

    public static function saveManyToMany(&$parent, $newChildrenIds, $config) {
	    $relationName = $config['relationName'];
	    $childIdAttr = $config['childIdAttr'];
	    $parentIdAttr = $config['parentIdAttr'];
        foreach ($parent->$relationName as $link) {
            // Remove deleted options
            if (!in_array($link->$childIdAttr, $newChildrenIds)) {
                $link->delete();
            } else {
                // Remove from temporary array options already selected
                $newChildrenIds = array_diff($newChildrenIds, [$link->$childIdAttr]);
            }
        }
        foreach ($newChildrenIds as $itemId) {
            $optionPersonLink = new $config['relationModel']();
            $optionPersonLink->$parentIdAttr = $parent->id;
            $optionPersonLink->$childIdAttr = $itemId;
            $optionPersonLink->save();
        }
    }

    /**
     * Get a DSN attribute from database connection
     *
     * @param string $name
     * @param \yii\db\Connection $dsn
     * @return null
     */
    private static function _getDsnAttribute($name, $db) {
        if (preg_match('/' . $name . '=([^;]*)/', $db->dsn, $match)) {
            return $match[1];
        } else {
            return null;
        }
    }
}
