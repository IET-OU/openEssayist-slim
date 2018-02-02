<?php namespace IET_OU\OpenEssayist\Utils;

/**
 * Commandline wrapper to 'Application' and 'DBConnection'.
 *
 * @package   OpenEssayist-slim
 * @copyright © 2013-2018 The Open University. (Institute of Educational Technology)
 * @author    Nick Freear, 02-February-2018.
 */

use IET_OU\OpenEssayist\Utils\DBConnection;

class CliApp extends DBConnection
{
  const SCHEMA_FILE = __DIR__ . '/../_data/openessayist-schema.sql';

  public static function cliCheck()
  {
    if ('cli' !== php_sapi_name()) {
      header( 'HTTP/1.1 500' );
      die( 'CLI only.' );
    }

    // define( 'CLI', true );
  }

  /**
   * @param string $try_arg Does the commandline argument exist?
   * @return string
   */
  public static function arg( $try_arg )
  {
    global $argv, $argc;

    $last_arg = $argv[ $argc - 1 ];
    return $last_arg === $try_arg;
  }

  /**
   * Create a database.
   * @return boolean Success?
   */
  public static function createDatabase()
  {
    $dbh = self::dbconfig( 'dbh' );  // $GLOBALS[ 'openessayist_dbh' ];
    $database = self::dbconfig( 'database' );

    $result = $dbh->exec("CREATE DATABASE IF NOT EXISTS `$database`;")
  	or die(print_r($dbh->errorInfo(), true));

    return $result;
  }

  /**
   * Create a database tables, based on the schema file.
   * @return boolean Success?
   */
  public static function createTables()
  {
    $orm = \ORM::get_db();

    $sql = file_get_contents( self::SCHEMA_FILE );
    $database = self::dbconfig( 'database' );
    //try {
    $result = $orm->exec( $sql );
    /*}
    catch (\PDOException $ex) {
      // Table exists ??
      Clio::error('Database Error: ' . $ex->getMessage());
      exit( 1 );
    }*/
    return $result;
  }

  /**
   * @return int  Return a count of tables in the database.
   */
  public static function countTables()
  {
    $database = self::dbconfig( 'database' );
    $count_tables = \ORM::for_table( 'information_schema.tables' )->where( 'table_schema', $database )->count();
    // $result = ORM::raw_execute( "USE $database; SHOW TABLES; SELECT found_rows() AS count;");
    // $statement = ORM::get_last_statement();
    return $count_tables;
  }
}
