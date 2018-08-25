<?php

return [
  'id' => 'soundboard-tijsmoree',
  'basePath' => dirname(__DIR__),
  'components' => [
    'cache' => [
      'class' => 'yii\caching\FileCache'
    ],
    'db' => require(__DIR__ . '/db.php')
  ]
];
