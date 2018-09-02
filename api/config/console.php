<?php

return [
    'id' => 'soundboard-tijsmoree',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'app\commands',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'db' => require(__DIR__ . '/db.php')
    ]
];
