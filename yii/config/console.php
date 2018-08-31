<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

return [
    'id' => 'etv-ledendb',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'gii'],
    'controllerNamespace' => 'app\commands',
    'modules' => [
        'gii' => 'yii\gii\Module',
        'membermodels' => [
            'class' => 'app\modules\membermodels\MembermodelsModule'
        ],
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/_db.php')
    ],
    'controllerMap' => [
        'migrate' => [
            'class' => 'dmstr\console\controllers\MigrateController',
        ],
    ],
    'params' => require(__DIR__ . '/_params.php'),
];
