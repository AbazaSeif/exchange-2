<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
        'name'=>'Lbr exchange',
        'sourceLanguage' => 'ru',
        'language' => 'ru',
	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
                'application.extensions.*',
                'application.helpers.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'admin',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
                'user',
                'admin',
	),
        'preload'=>array('log'),
	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
                        'class' => 'WebUser',
			'allowAutoLogin'=>true,
		),
		// uncomment the following to enable URLs in path-format
		'urlManager'=>array(
			'urlFormat'=>'path',
                        'showScriptName'=>false,
			'rules'=>array(
			    'transport/i/<page:\d+>'=>'transport/i',
                '<_m:user>/<_a:\w+>' => 'user/default/<_a>',
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
				'transport/i/<page:\d+>'=>'transport/i',
			),
		),
		
		'db'=>array(
                        'class'=>'CDbConnection',
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/exchange.db',
                        'initSQLs'=>array(
                            'PRAGMA foreign_keys = ON',
                        ),
                        'enableProfiling'=>true,
                        'enableParamLogging' => true,
		),
//                'db_user'=>array(
//                        'class'=>'CDbConnection',
//			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../../auth/data/auth.db',
//                        'initSQLs'=>array(
//                            'PRAGMA foreign_keys = ON',
//                        ),
//                        'enableProfiling'=>true,
//                        'enableParamLogging' => true,
//		),
            // uncomment the following to use a MySQL database
		/*
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=chat',
			'emulatePrepare' => true,
			'username' => 'mysql',
			'password' => 'mysql',
			'charset' => 'utf8',
		),*/
		
		// uncomment the following to use a MySQL database
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
                    'class'=>'CLogRouter',
                    'routes'=>array(
                      array(
//                        'class'=>'CWebLogRoute',  'levels'=>'trace, info, error, warning',
                        'class'=>'CWebLogRoute',  'levels'=>'info, error, warning',
                      ),
                      array(
                        'class'=>'CFileLogRoute',  'levels'=>'trace, info, error, warning',
                      ),
                    )
                  ),
                'authManager'=>array(
                    'class'=>'CDbAuthManager',
                    'connectionID'=>'db',
                ),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
            // this is used in contact page
            'meta_title'=>'Биржа перевозок',
            'meta_description'=>'перевозки, биржа перевозок, биржа ЛБР',
            'menu_admin'=>array(
                    'Пользователи'=>array(
                            'Пользователи'=>'/admin/user/',
                            'Группы пользователей'=>'/admin/user/group/',
                            'Роли'=>'/admin/user/role/',
                            'Операции'=>'/admin/user/operation/',
                    ),
                    'Перевозки'=>'/admin/transport/',
             ),
             'adminEmail'=>'krilova@lbr.ru',
             'minNotyfy' => 30,
             'hoursBefore' => 48,
             'ferrymanGroup' => 4,
             'nds' => 0.1, // 10%
	),
);
