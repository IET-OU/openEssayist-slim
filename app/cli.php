#!/usr/bin/env php
<?php
/**
 * Command-line CLI database tool - create and seed DB tables.
 *
 * @package   OpenEssayist-slim
 * @copyright © 2013-2018 The Open University. (Institute of Educational Technology)
 * @author    Nick Freear, 24-January-2018.
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

define( 'CLI', true );

require_once __DIR__ . '/utils/autoload.php';

use Clio\Console as Clio;
use IET_OU\OpenEssayist\Utils\CliApp as oeApp;

oeApp::cliCheck();

Clio::output('>> OpenEssayist CLI.' . PHP_EOL);
Clio::output('Schema: ' . oeApp::SCHEMA_FILE);

oeApp::dbconnect();

if (oeApp::databaseExists()) {
  oeApp::userTableExists();
}

if (oeApp::arg( '--create-db' )) {

  $result = oeApp::createDatabase();

  Clio::output('>> OK. Database created: '. $database);
  print_r( $result );
  exit;
}

if (oeApp::arg( '--create-tables' )) {
  try {
    $result = oeApp::createTables();
  }
  catch (\PDOException $ex) {
    Clio::error('Database Error: ' . $ex->getMessage()); // Table exists ??
    exit( 1 );
  }
  Clio::output('>> OK. Tables created. Schema: ' . oeApp::SCHEMA_FILE);

  print_r( $result );
  exit;
}

if (oeApp::arg( '--count-tables' )) {
  Clio::output('>> Count of tables: ' . oeApp::countTables());
  exit;
}

if (oeApp::arg( '--seed-users' )) {
  $users = oeApp::config( 'users' );
  print_r( $users );

  $strong = new Strong\Strong([ 'provider' => 'PDO', 'pdo' => ORM::get_db(), ]);
  foreach ($users as $user) {
    try {
      $result = oeApp::createUser( $user );
    }
    catch (\PDOException $ex) {
      print_r($ex->getMessage());
    }
    Clio::output('>> OK. User added, username: ', $user->username);
    print_r( $u->get( 'username' ), $u->get( 'id' ) );
  }

  exit;
}

if (oeApp::arg( '--seed-groups' )) {
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

if (oeApp::arg( '--seed-tasks' )) {
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
