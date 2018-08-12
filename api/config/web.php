<?php

$db = require(__DIR__ . '/db.php');

return [
  'id' => 'yii2mini',
  'basePath' => dirname(__DIR__),
  'bootstrap' => ['log'],
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
    'log' => [
      'traceLevel' => YII_DEBUG ? 3 : 0,
      'targets' => [
        [
          'class' => 'yii\log\FileTarget',
          'levels' => ['error', 'warning']
        ],
      ],
    ],
    'db' => $db
  ]
];
