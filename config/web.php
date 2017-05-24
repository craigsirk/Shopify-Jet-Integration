<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'SuperDuperSecretKey',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
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
                    'levels' => ['error'],
                	'logFile' => '/home/craigsir/webapps/shopify_app/Logs/' . date('m-d-y') . "-error.log",
                	'maxFileSize' => 1024 * 2,
                	'maxLogFiles' => 20,
                ],
            	[
            		'class' => 'yii\log\FileTarget',
            		'levels' => ['warning'],
            		'logFile' => '/home/craigsir/webapps/shopify_app/Logs/' . date('m-d-y') . "-warning.log",
            		'maxFileSize' => 1024 * 2,
            		'maxLogFiles' => 20,
            	],
            	[
            		'class' => 'yii\log\FileTarget',
            		'levels' => ['trace'],
            		'categories'=>['jet*', 'shopify*'],
            		'logFile' => '/home/craigsir/webapps/shopify_app/Logs/trace.log',
            		'maxFileSize' => 1024 * 2,
            		'maxLogFiles' => 20,
            	],
            	[
            		'class' => 'yii\log\DbTarget',
            		'levels' => ['info'],
            		'logTable'=>'Yii2_infoLogs',
            		'categories'=>['jet.*', 'shopify.*'],
            		'logVars' => [],
            		/*
            		In order to capture the store info and adjust the log, needed to modify
            		yiisoft/yii2/log/Target.php line 94
            		yiisoft/yii2/log/Target.php line 313 new function getMessageStore
            		yiisoft/yii2/log/DbTarget.php line 64-83
            		 */
            	],
            	[
            		'class' => 'yii\log\DbTarget',
            		'levels' => ['error'],
            		'logTable'=>'Yii2_errorLogs',
            		'categories'=>['jet.*', 'shopify.*'],
            		'logVars' => [],
            		/*
            		 In order to capture the store info and adjust the log, needed to modify
            		yiisoft/yii2/log/Target.php line 94
            		yiisoft/yii2/log/Target.php line 313 new function getMessageStore
            		yiisoft/yii2/log/DbTarget.php line 64-83
            		*/
            	],
            		
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'urlManager' => [
	        'class' => 'yii\web\UrlManager',
	        // Disable index.php
	        'showScriptName' => false,
	        // Disable r= routes
	        'enablePrettyUrl' => true,
	        'rules' => array(
	                '<controller:\w+>/<id:\d+>' => '<controller>/view',
	                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
	                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
	        		'secondcontroller/<action:.*>'=>'secondcontroller/<action>',
	        		'<action:.*>'=>'site/<action>',
	        ),
        ],
    ],
    'params' => $params,
	'modules' => [
		'gridview' =>  [
			'class' => 'kartik\grid\Module'
			// enter optional module parameters below - only if you need to
			// use your own export download action or custom translation
			// message source
			// 'downloadAction' => 'gridview/export/download',
			// 'i18n' => []
		]
	]
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
