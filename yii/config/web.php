<?php

$config = [
    'id' => 'etv-ledendb',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['auth', 'log'],
    'modules' => [
        'membermodels' => [
            'class' => 'app\modules\membermodels\MembermodelsModule'
        ],
    ],
    'components' => [
        'auth' => [
            'class' => 'app\components\Auth',
            'siteAlias' => 'ledendb'
        ],
        'etvipAccess' => [
            'class' => 'app\components\TokenAccessControl',
            'db' => require(__DIR__ . '/_db_access.php'),
            'siteAlias' => 'etvip'
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'members/committee/image' => 'image/committee',
                'members/person/image' => 'image/person',
                'boards/default/image' => 'image/board',
                'etvip' => 'default/etvip'
            ],
        ],
        'request' => [
            'cookieValidationKey' => 'qwrawgikuil7humnkjh',
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
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/_db.php')
    ],
    'params' => require(__DIR__ . '/_params.php'),
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
