<?php namespace IET_OU\OpenEssayist\Utils;

/**
 * OpenEssayist-slim.
 *
 * @copyright © 2013-2018 The Open University. (Institute of Educational Technology)
 */

// Was in:  `app/config.php`.

class DBConnection extends \Application
{
  public static function connect()
  {
    $activeGroup = 'local';

    /**
     * Needed in the RHEL distribution of Apache/PHP for the use of date();
     */
    date_default_timezone_set( 'Europe/London' );

    try {
    	// echo "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'DBName'";
    	$host = self::config( 'hostname', 'db' );  // Was: $db[$activeGroup]['hostname'];
    	$root = self::config( 'username', 'db' );
    	$root_password= self::config( 'password', 'db' );
    	$database= self::config( 'database', 'db' );

    	$dbh = new \PDO("mysql:host=$host", $root, $root_password);

      if (defined( 'CLI' )) {
    		$GLOBALS[ 'db' ][ $activeGroup ][ 'dbh' ] = $dbh;

    		/* $GLOBALS[ 'openessayist_dbh' ] = $dbh;
    		$GLOBALS[ 'openessayist_database' ] = $database;
    		*/
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
}
