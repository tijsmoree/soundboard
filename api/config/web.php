<?php

$db = require(__DIR__ . '/db.php');

return [
  'id' => 'soundboard-tijsmoree',
  'basePath' => dirname(__DIR__),
  'components' => [
    'request' => [
      'cookieValidationKey' => 'pfwappomgepaoemomcppjcp0w4054u9tqjfm2qqkjf'
    ],
    'cache' => [
      'class' => 'yii\caching\FileCache'
    ],
    'urlManager' => [
      'enablePrettyUrl' => true,
      'showScriptName' => false
    ],
    'db' => $db
  ]
];
