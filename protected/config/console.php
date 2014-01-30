<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Console Application',
	// preloading 'log' component
	'preload'=>array('log'),
        'import' => array(
	    'application.models.*',
            'application.helpers.*',
	),
	// application components
	'components'=>array(
            'db'=>array(
                    'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/exchange.db',
            ),
            // uncomment the following to use a MySQL database

            /*'db'=>array(
                    'connectionString' => 'mysql:host=localhost;dbname=chat',
                    'emulatePrepare' => true,
                    'username' => 'mysql',
                    'password' => 'mysql',
                    'charset' => 'utf8',
            ),*/

            'log'=>array(
                    'class'=>'CLogRouter',
                    'routes'=>array(
                            array(
                                    'class'=>'CFileLogRoute',
                                    'levels'=>'error, warning',
                            ),
                    ),
            ),
	),
	'params'=>array(
            // this is used in contact page
            'adminEmail'=>'krilova@lbr.ru',
            'minNotyfy' => 30,
            'hoursBefore' => 24, 
	),
);