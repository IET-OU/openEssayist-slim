<?php

/**
 * OpenEssayist database details.
 *
 * $activeGroup contains key for current databases
 * $db contains all database sets available
 * Change $activeGroup value to one of the $db keys, to activate a different database.
 *
 * NOTES:
 *  - make sure that the DB defined in 'database' field is created in MySQL
 *  - make sure that the directory defined in 'logdir' is created (and contains the log files)
 */

$activeGroup = 'local';

// local database and logs
$db['local']['hostname'] = 'localhost';
$db['local']['username'] = '** EDIT ME **';
$db['local']['password'] = '** EDIT ME **';
$db['local']['database'] = 'openessayist';
$db['local']['dbProvider'] = 'mysql';
$db['local']['logdir'] = __DIR__ . '/../.logs';

// H810 archive database and logs
$db['H810']['hostname'] = 'localhost';
$db['H810']['username'] = '** EDIT ME **';
$db['H810']['password'] = '** EDIT ME **';
$db['H810']['database'] = 'openh810';
$db['H810']['dbProvider'] = 'mysql';
$db['H810']['logdir'] = __DIR__ . '/../log-h810';

// H817 archive database and logs
$db['H817']['hostname'] = 'localhost';
$db['H817']['username'] = '** EDIT ME **';
$db['H817']['password'] = '** EDIT ME **';
$db['H817']['database'] = 'openh817';
$db['H817']['dbProvider'] = 'mysql';
$db['H817']['logdir'] = __DIR__ . '/../log-h817';

// Other configuration.
$seedDatabase = false;
$email = 'openessayist-techsupport@open.ac.uk'; // 'nicolas.vanlabeke@open.ac.uk'
$analyserUrl = 'http://127.0.0.1:8062';
$rdSavePath = __DIR__ . '/../_user_data/images/';

// ------------------------------------------------------------------------
// TODO: move!

/**
 * Needed in the RHEL distribution of Apache/PHP for the use of date();
 */
date_default_timezone_set('Europe/London');

try {
	//echo "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'DBName'";
	$host = $db[$activeGroup]['hostname'];
	$root = $db[$activeGroup]['username'];
	$root_password= $db[$activeGroup]['password'];
	$database= $db[$activeGroup]['database'];

	$dbh = new PDO("mysql:host=$host", $root, $root_password);

	$dbh->exec("CREATE DATABASE IF NOT EXISTS `$database`;")
	or die(print_r($dbh->errorInfo(), true));

} catch (PDOException $e) {
	die("DB ERROR: ". $e->getMessage());
}

$providerString = sprintf('mysql:host=%s;dbname=%s', $db[$activeGroup]['hostname'], $db[$activeGroup]['database']);
ORM::configure($providerString);
ORM::configure('username', $db[$activeGroup]['username']);
ORM::configure('password', $db[$activeGroup]['password']);
