<?php

return [
    'id' => 'soundboard-tijsmoree',
    'basePath' => dirname(__DIR__),
    'components' => [
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [],
        ],
        'request' => [
            'cookieValidationKey' => 'w09fa09fwj0fa9j9fwafawvecghrsVCEEC3apjapDO',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'db' => require(__DIR__ . '/db.php')
    ]
];
