<?php

return CMap::mergeArray(
	require(dirname(__FILE__) . '/database.php'),
    require(dirname(__FILE__) . '/airbrake.php'),
	array(
        'aliases' => array(
            'Airbrake' => 'webroot.vendor.dbtlr.php-airbrake.src.Airbrake', // This is needed for the namespacing to work
        ),
		'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
		'name' => 'bascibot {\'<\'}',
		'preload' => array('log'),
		'import' => array(
			'application.models.*',
			'application.components.*',
		),
		'modules' => array(
			'gii' => array(
				'class' => 'system.gii.GiiModule',
				'password' => 'hoge',
			),
		),
		'components' => array(
			'user' => array(
				'allowAutoLogin' => true,
			),
			'urlManager' => array(
				'urlFormat' => 'path',
				'rules' => array(
                    '<server:.+?>/<channel:\w+>/<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                    '<server:.+?>/<channel:\w+>/<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                    '<server:.+?>/<channel:\w+>/<controller:\w+>/' => '<controller>/index',
				),
				'showScriptName' => false,
			),
			'errorHandler' => array(
				'errorAction' => 'site/error',
			),
			'log' => array(
				'class' => 'CLogRouter',
				'routes' => array(
					array(
						'class' => 'CFileLogRoute',
						'levels' => 'error, warning',
					),
				),
			),
			'widgetFactory' => array(
				'widgets' => array(
					'CLinkPager' => array(
						'maxButtonCount' => 5,
						'cssFile' => false,
					),
					'CGridView' => array(
						'cssFile' => false,
					),
					'CJuiDatePicker' => array(
						'language' => 'ja',
					),
				),
			),
		),
		'params' => array(
			'adminEmail' => 'webmaster@example.com',
		),
	)
);
