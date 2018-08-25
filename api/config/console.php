<?php

return [
  'id' => 'soundboard-tijsmoree',
  'basePath' => __DIR__ . '/../',
  'components' => [
    'cache' => [
      'class' => 'yii\caching\FileCache'
    ],
    'db' => require(__DIR__ . '/db.php')
  ]
];
