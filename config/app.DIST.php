<?php
/**
 * OpenEssayist - database and application configuration.
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

$_oe_email_ = 'openessayist-techsupport@open.ac.uk'; // Was: 'nicolas.vanlabeke@open.ac.uk'

$openessayist_config = [
	'email' => $_oe_email_,
	'analyser_url' => 'http://127.0.0.1:8062',
	'rd_save_path' => __DIR__ . '/../_user_data/images/',
	'draft_maxlength_chars' => 30 * 1000, // Was: 4000, (set after Simon Cross briefing).
	'_max_word_count_' => 5000,
	'task_code_regex' => '^(TMA01|ICS)$',  // Regex for use in Javascript & PHP.
	'group_code_regex'=> '^(H810|VCS)$',

	// SAMS authentication - only enable for Open University.
	'sams_enable' => false,
	'sams_redirect' => true,
	'sams_password' => '** EDIT ME **',
	'sams_group_id' => 1,  // Set a default.
	// See :~ https://github.com/IET-OU/sams-c-auth/blob/master/src/SamsCAuth.php#L29
	// NOT:  /^(...|visitor)$/
	'sams_auth_regex' => '/^(staff|tutor|student)$/',
	// Accounts in this admin list have the "isadmin" and "isgroup" flags set on their account.
	'admin_oucu_list' => [ 'abc123', '** EDIT ME **' ],
	'analyser_version' => __DIR__ . '/../../openessayist-python/version.json',

	'google_analytics' => 'UA-3845152-23', // GA property within 'IET Sites'.
	'analytics_prefix' => '/local',        // Example: '/server-A', '/server-B'. ** EDIT ME **

	// Seed database ... 1+ users, 1+ groups, 1+ tasks.

	'users' => [
		'admin' => (object) [
			'username' => 'admin',
			'password' => '** EDIT ME **',
			'email'  => $_oe_email_,
			'name' => 'Admin 01',
			'isadmin' => true,
			'auth_type' => 'seed',
		],
	],

	'groups' => [
		'default' => (object) [
			'name' => 'Default',
			'code' => 'H810',  // Must be 'H810', or 'VSO'.
			'description' => 'A default group.',
		]
	],

	'tasks' => [
		'task-01' => (object) [
			'name' => 'Task 01',
			'code' => 'TMA01',  // Must be 'TMA01 or ICS'.
			'assignment' => 'A default assignment.', // Open by default.
		]
	]
];


// ------------------------------------------------------------------------
// The database connection code has been moved to `app/utils/DBConnection.php` (2-Feb-2018).
// End.
