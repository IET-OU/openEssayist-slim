#!/usr/bin/env php
<?php
/**
 * Command-line CLI database tool - create and seed DB tables.
 *
 * OpenEssayist-slim.
 *
 * @copyright © 2013-2018 The Open University. (Institute of Educational Technology)
 * @author   NDF, 24-Jan-2018.
 */

$__HELP__ = <<<EOH

Help / actions:

  app/cli.php --create-db     # Needs root perms ?!
  app/cli.php --create-tables
  app/cli.php --count-tables  # Needs root perms ?!
  app/cli.php --seed-groups
  app/cli.php --seed-users
  app/cli.php --seed-tasks

EOH;

if ('cli' !== php_sapi_name()) {
  die( 'CLI only.' );
}

define( 'CLI', true );
define( 'SCHEMA_FILE', __DIR__ . '/_data/openessayist-schema.sql' );

$last_arg = $argv[ $argc - 1 ];

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/application.php';
require_once __DIR__ . '/models/users.model.php';  // Contains multiple models!

use Clio\Console as Clio;
use Application as oeApp;

Clio::output('>> OpenEssayist CLI.' . PHP_EOL);
Clio::output('Schema: ' . SCHEMA_FILE);
// echo "CLI start: $last_arg \nSchema: " . SCHEMA_FILE . PHP_EOL;

if (oeApp::databaseExists()) {
  oeApp::userTableExists();
}

if ( '--create-db' === $last_arg ) {
  //$orm = ORM::get_db();

  $dbh = oeApp::config( 'dbh', 'db' ); // $GLOBALS[ 'openessayist_dbh' ];
  $database = oeApp::config( 'database', 'db' ); // $GLOBALS[ 'openessayist_database' ];

  $result = $dbh->exec("CREATE DATABASE IF NOT EXISTS `$database`;")
	or die(print_r($dbh->errorInfo(), true));

  Clio::output('>> OK. Database created: '. $database);

  print_r( $result );
  exit;
}

if ( '--create-tables' === $last_arg ) {
  $orm = ORM::get_db();

  $sql = file_get_contents( SCHEMA_FILE );
  $database = oeApp::config( 'database', 'db' );
  try {
    $result = $orm->exec( $sql );
  }
  catch (\PDOException $ex) {
    // Table exists ??
    Clio::error('Database Error: ' . $ex->getMessage());
    exit( 1 );
  }
  Clio::output('>> OK. Tables created. Schema: ' . SCHEMA_FILE);

  print_r( $result );
  exit;
}

if ( '--count-tables' === $last_arg ) {
  $database = oeApp::config( 'database', 'db' );
  $count_tables = ORM::for_table( 'information_schema.tables' )->where( 'table_schema', $database )->count();
  // $result = ORM::raw_execute( "USE $database; SHOW TABLES; SELECT found_rows() AS count;");
  // $statement = ORM::get_last_statement();

  Clio::output('>> Count of tables: ' . $count_tables);
  exit;
}

if ( '--seed-users' === $last_arg ) {
  $users = oeApp::config( 'users' );
  print_r( $users );

  $strong = new Strong\Strong([ 'provider' => 'PDO', 'pdo' => ORM::get_db(), ]);
  foreach ($users as $user) {
    $u = Model::factory('Users')->create();
    $u->username = $user->username;
    $u->password = Strong\Strong::getInstance()->getProvider()->hashPassword( $user->password );
    $u->email = $user->email;
    $u->name = $user->name;
    $u->isadmin = $user->isadmin;
    $u->active = 1;
    $u->isdemo = 0;
    $u->auth_type = $user->auth_type;
    $u->ip_address = ''; //$this->app->request()->getIp();
    $u->group_id = 1; // $gid;

    try {
      $u->save();
    }
    catch (\PDOException $ex) {
      print_r($ex->getMessage());
    }
    Clio::output('>> OK. User added, username: ', $user->username);
    print_r( $u->get( 'username' ), $u->get( 'id' ) );
  }

  exit;
}

if ( '--seed-groups' === $last_arg ) {
  $groups = oeApp::config( 'groups' );
  print_r( $groups );

  foreach ($groups as $group) {
    $gp = Model::factory('Group')->create();
    $gp->name = $group->name;
    $gp->code = $group->code;
    $gp->description = $group->description;

    try {
      $gp->save();
    }
    catch (\PDOException $ex) {
      print_r($ex->getMessage());
    }
    Clio::output('>> OK. Group added, name: ', $group->name);

    print_r( $gp->get( 'name' ), $gp->get( 'id' ) );
  }
  exit;
}

if ( '--seed-tasks' === $last_arg ) {
  $tasks = oeApp::config( 'tasks' );
  print_r( $tasks );

  foreach ($tasks as $task) {
    $tk = Model::factory('Task')->create();
    $tk->name = $task->name;
    $tk->code = $task->code;
    $tk->assignment = $task->assignment;
    $tk->deadline = date( 'Y-m-d', strtotime( '+1 month' )); // MySQL DATE.
    $tk->wordcount = 2000;
    $tk->isopen = true;
    $tk->group_id = 1;

    try {
      $tk->save();
    }
    catch (\PDOException $ex) {
      print_r($ex->getMessage());
    }
    Clio::output('>> OK. Task added, name: ', $task->name);

    print_r( $tk->get( 'name' ), $tk->get( 'id' ) );
  }
  exit;
}

Clio::output($__HELP__);
Clio::output('CLI end');
