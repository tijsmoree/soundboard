<?php

$db = require(__DIR__ . '/db.php');

return [
  'id' => 'yii2mini',
  'basePath' => dirname(__DIR__),
  'components' => [
    'request' => [
      'cookieValidationKey' => 'pfwappomgepaoemomcppjcp0w4054u9tqjfm2qqkjf'
    ],
    'cache' => [
      'class' => 'yii\caching\FileCache'
    ],
    'errorHandler' => [
      'errorAction' => 'site/error',
    ],
    'urlManager' => [
      'class' => 'yii\web\UrlManager',
      'enablePrettyUrl' => true,
      'showScriptName' => false
    ],
    'db' => $db
  ]
];
