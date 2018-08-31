<?php
/**
 * The Yii2 Component which will provide access control.
 * User: Paul Marcelis
 * Date: 8-5-2016
 * Time: 19:16
 */

namespace app\components;

use yii\web\HttpException;
use yii\db\Connection;
use Yii;

class TokenAccessControl {

    public $siteAlias = '';
    public $db = [
        'dsn' => 'mysql:host=DBHOST;dbname=DBNAME',
        'username' => 'DBUSERNAME',
        'password' => 'DBPASSWORD',
        'charset' => 'utf8',
    ];

    private static $_tokenCheckQuery = "SELECT *
        FROM user_tokens t
        INNER JOIN users u ON u.id = t.user_id
        INNER JOIN user_access a ON a.user_id = u.id
        INNER JOIN sites s ON s.id = a.site_id
        WHERE t.token = :token
        AND s.alias = :site_alias
        AND u.deleted_at IS NULL
        AND s.deleted_at IS NULL
        AND t.updated_at >= DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 HOUR)";

    public function beforeAction($action) {
        if (!LIVE) {
            return true;
        }

        $token = Yii::$app->request->headers->get('DbAccess-Token', null);
        if ($token === null) {
            throw new HttpException(401);
        }

        unset($this->db['class']);
        $db = new Connection($this->db);
        $db->open();
        $cmd = $db->createCommand(static::$_tokenCheckQuery);
        $cmd->bindValue(':token', $token);
        $cmd->bindValue(':site_alias', $this->siteAlias);
        $result = $cmd->queryOne();


        if ($result == null) {
            throw new HttpException(401);
        }

        return true;
    }
}

