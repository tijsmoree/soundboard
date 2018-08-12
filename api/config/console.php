<?php

$db = require(__DIR__ . '/db.php');

return [
  'id' => 'yii2mini-console',
  'basePath' => dirname(__DIR__),
  'controllerNamespace' => 'app\commands',
  'components' => [
    'cache' => [
      'class' => 'yii\caching\FileCache'
    ],
    'db' => $db
  ]
];
