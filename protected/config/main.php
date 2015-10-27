<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'Lbr exchange',
    'timeZone' => 'Europe/Minsk',
    'sourceLanguage' => 'ru',
    'language' => 'ru',
    // preloading 'log' component
    'preload' => array('log'),
    // autoloading model and component classes
    'import' => array(
        'application.models.*',
        'application.components.*',
        'application.extensions.*',
        'application.helpers.*',
    ),

    'modules' => array(
        // uncomment the following to enable the Gii tool
        /*'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => 'admin',
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            'ipFilters' => array('127.0.0.1', '::1'),
        ),*/
        'user',
        'admin',
    ),
    'preload' => array('log'),
    // application components
    'components' => array(
        'user' => array(
            // enable cookie-based authentication
            'class' => 'WebUser',
            'allowAutoLogin' => true,
        ),
        'session' => array(
            'class' => 'HttpSession',
        ),
        // uncomment the following to enable URLs in path-format
        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,
            'rules' => array(
                'transport/i/<page:\d+>' => 'transport/i',
                '<_m:user>/<_a:\w+>' => 'user/default/<_a>',
                '<_a:(feedback|help)>'=>'site/<_a>',
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                'transport/i/<page:\d+>' => 'transport/i',
            ),
        ),

        'db' => array(
            'class' => 'CDbConnection',
            'connectionString' => 'sqlite:' . dirname(__FILE__) . '/../../../data/exchange.db',
            'initSQLs' => array(
                'PRAGMA foreign_keys = ON',
            ),
            'enableProfiling' => true,
            'enableParamLogging' => true,
        ),
        'db_auth' => array(
            'class' => 'CDbConnection',
            'connectionString' => 'sqlite:' . dirname(__FILE__) . '/../../../data/auth.db',
            'initSQLs' => array(
                'PRAGMA foreign_keys = ON',
            ),
        ),
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
        'errorHandler' => array(
            // use 'site/error' action to display errors
            'errorAction' => 'site/error',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                /*array(
                    'class' => 'CWebLogRoute', 'levels' => 'info, error, warning',
                ),*/
                array(
                    'class' => 'CFileLogRoute', 'levels' => 'info, error, warning',
                ),
            )
        ),
        'authManager' => array(
            'class' => 'CDbAuthManager',
            'connectionID' => 'db_auth',
        ),
        'search'=>array(
            'class'=>'SearchComponent',
        )
    ),

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(
        // this is used in contact page
        'host'=>'lbr.test',
        //'host'=>'lbr.ru',
        'meta_title' => 'Биржа перевозок',
        'meta_description' => 'перевозки, биржа перевозок, биржа ЛБР',
        'menu_admin' => array(
            'Перевозчики'=>array(
                'Компании'=>'/admin/user/',
                'Контактные лица'=>'/admin/contact/',
                'повтор ИНН'=>'/admin/dubinn/',
                'повтор Email'=>'/admin/dubemail/',
            ),
            'Перевозки' => '/admin/transport/',
            'Статистика' => '/admin/statistics/',
            'Журнал редактирования' => '/admin/changes/',
        ),

        'supportEmail' => 'support.ex@lbr.ru',
        'adminEmail'   => 'help.ex@lbr.ru',
        'logistEmailRegional'       => 'golovachenko@lbr.ru',
        'logistEmailInternational'  => 'budaeva@lbr.ru',
        
        'minNotify' => 30,
        'hoursBefore' => 24,
        'ferrymanGroup' => 4,
        'nds' => 0.1, // 10%
    ),
);
