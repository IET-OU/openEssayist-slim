<?php namespace IET_OU\OpenEssayist\Utils;

/**
 * Create the database connection.
 *
 * @package   OpenEssayist-slim
 * @copyright © 2013-2018 The Open University. (Institute of Educational Technology)
 */

// Was in:  `app/config.php`.

class DBConnection extends \Application
{
  public static function dbconfig( $key )
  {
    return self::config( $key, 'db' );
  }

  /** Connect to the database.
   */
  public static function dbconnect()
  {
    $activeGroup = 'local';

    /** Needed in the RHEL distribution of Apache/PHP for the use of date();
     */
    date_default_timezone_set( 'Europe/London' );

    try {
    	// echo "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'DBName'";
      $host = self::dbconfig( 'hostname', 'db' );  // Was: $db[$activeGroup]['hostname'];
      $root = self::dbconfig( 'username', 'db' );
      $root_password= self::dbconfig( 'password', 'db' );
      $database= self::dbconfig( 'database', 'db' );

    	$dbh = new \PDO("mysql:host=$host", $root, $root_password);

      if (defined( 'CLI' )) {
    		$GLOBALS[ 'db' ][ $activeGroup ][ 'dbh' ] = $dbh;
     	}

    	// Was:  $dbh->exec("CREATE DATABASE IF NOT EXISTS `$database`;")
    	// Was:  or die(print_r($dbh->errorInfo(), true));

    } catch (\PDOException $ex) {
    	die("DB ERROR: ". $ex->getMessage());
    }

    $providerString = sprintf( 'mysql:host=%s;dbname=%s;charset=utf8', $host, $database );
    \ORM::configure( $providerString );
    \ORM::configure( 'username', $root );           // Was: $db[$activeGroup]['username']);
    \ORM::configure( 'password', $root_password );  // Was: $db[$activeGroup]['password']);
    \ORM::configure( 'driver_options', [ \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8' ]);
    \ORM::configure( 'logging', true);
  }

  /**
   * Create a user in the database.
   * @param object $user
   */
  public static function createUser( $user )
  {
    $u = \Model::factory('Users')->create();

    $u->username = $user->username;
    $u->password = self::hashPassword( $user->password );
    $u->email = $user->email;
    $u->name = $user->name;
    $u->isadmin = $user->isadmin;
    $u->active = 1;
    $u->isdemo = 0;
    $u->auth_type = $user->auth_type;
    $u->ip_address = ''; // $this->app->request()->getIp();
    $u->group_id = 1; // $gid;

    // try {
    $result = $u->save();
    /* }
    catch (\PDOException $ex) {
      print_r($ex->getMessage());
    } */
    return (object) [
      'result' => $result,
      'user' => $u,
    ];
  }

  public static function hashPassword( $password )
  {
    return \Strong\Strong::getInstance()->getProvider()->hashPassword( $password );
  }
}
