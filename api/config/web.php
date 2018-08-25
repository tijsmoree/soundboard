<?php

return [
  'id' => 'soundboard-tijsmoree',
  'basePath' => __DIR__ . '/../',
  'controllerNamespace' => 'api\controllers',
  'aliases' => [
    '@api' => __DIR__ . '/../',
  ],
  'components' => [
    'request' => [
      'cookieValidationKey' => 'pfwappomgepaoemomcppjcp0w4054u9tqjfm2qqkjf',
      'parsers' => [
        'application/json' => 'yii\web\JsonParser'
      ]
    ],
    'cache' => [
      'class' => 'yii\caching\FileCache'
    ],
    'urlManager' => [
      'enablePrettyUrl' => true,
      'showScriptName' => false
    ],
    'db' => require(__DIR__ . '/db.php')
  ]
];
