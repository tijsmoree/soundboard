<?php
/**
 * The Yii2 Component which will provide access control.
 * User: Paul Marcelis
 * Date: 8-5-2016
 * Time: 19:16
 */

namespace app\components;

use yii\base\Component;
use yii\web\HttpException;
use Yii;

class Auth extends Component {

    public $siteAlias = null;

    public function init() {
		if (LIVE) {
		    $allowedUrlBeginnings = [
		        '/api/mobile-app/',
                '/api/etvip'
            ];
			// skip if it is a mobile app request
            foreach ($allowedUrlBeginnings as $urlBeginning) {
                if (strpos($_SERVER['REDIRECT_URL'], $urlBeginning) === 0) {
                    return parent::init();
                }
            }
			// Use the following instead of existing session configurations
			session_name("db_access"); // Set the correct session name
			session_set_cookie_params(0, '/', '.etv.tudelft.nl'); // Set the correct session domain. So this probably only works for X.etv.tudelft.nl subdomains
			session_start(); // Start the session

			// Check of the site alias has been set
			if ($this->siteAlias === null) {
				throw new HttpException(401, 'The site alias for session authorization has not been set');
			}

			// Check the session variable whether the logged in user has rights. If not, go to login page
			if (!isset($_SESSION['db_access']) || !isset($_SESSION['db_access']['access'][$this->siteAlias ])) {
				throw new HttpException(401, 'No authorization given');
			}
		}
		
		return parent::init();
    }
}

