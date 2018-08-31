<?php
require(dirname(dirname(dirname(__FILE__)))."/global.php");

switch(gethostname()) {
	case 'JOSEPHVERBURG':
    case 'Cerebroso': //PC Marcelis
    case 'HERCULES': //Laptop Marcelis
        define('IMAGE_DIR', dirname(dirname(__DIR__)) . '/files');
        defined('YII_DEBUG') or define('YII_DEBUG', true);
        defined('YII_ENV') or define('YII_ENV', 'local');
        break;
    default:
        define('IMAGE_DIR', '/mnt/web_content/ledendb');
        defined('YII_DEBUG') or define('YII_DEBUG', true);
        defined('YII_ENV') or define('YII_ENV', 'live');

}

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../config/web.php');

(new yii\web\Application($config))->run();
