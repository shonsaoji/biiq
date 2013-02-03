<?php
date_default_timezone_set('America/Vancouver');

set_include_path('.' . PATH_SEPARATOR . './library/'
	 . PATH_SEPARATOR . './application/models'
	 . PATH_SEPARATOR . get_include_path());
include "library/Zend/Loader.php";
Zend_Loader::loadClass('Zend_Controller_Front');
Zend_Loader::loadClass('Zend_Registry');
Zend_Loader::loadClass('Zend_View');
Zend_Loader::loadClass('Zend_Config_Ini');
Zend_Loader::loadClass('Zend_Db');
Zend_Loader::loadClass('Zend_Db_Table');
Zend_Loader::loadClass('Zend_Debug');
Zend_Loader::loadClass('Zend_Json');
Zend_Loader::loadClass('Zend_Db_Adapter_Pdo_Mysql');

// setup database
/*
$db = new Zend_Db_Adapter_Pdo_Mysql(array(
    'host'     => '127.0.0.1',
    'username' => 'sethkutt_sethkut',
    'password' => 'pjIc1kSE',
    'dbname'   => 'sethkutt_testing'
));
*/

$db = new Zend_Db_Adapter_Pdo_Mysql(array(
		'host'     => 'db453890854.db.1and1.com',
		'username' => 'dbo453890854',
		'password' => 'testing',
		'dbname'   => 'db453890854'
));

Zend_Db_Table::setDefaultAdapter($db);

// setup controller
$frontController = Zend_Controller_Front::getInstance();
$frontController->throwExceptions(true);
#$frontController->setBaseUrl('/biiq');
$frontController->setControllerDirectory('./application/controllers');

// run!

$frontController->dispatch();