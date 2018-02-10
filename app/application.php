<?php
/**
 * Base / core application class.
 *
 * @package   OpenEssayist-slim
 * @copyright © 2013-2018 The Open University. (Institute of Educational Technology)
 * @author Nicolas Van Labeke (https://github.com/vanch3d)
 */

if (! defined( 'CLI' )) { define( 'CLI', false ); }

class Application {

	/**
	 *
	 * @var \Slim\Slim
	 */
	public $app;

	/**
	 *
	 * @var PDO
	 */
	public $db;

	public function __construct(\Slim\Slim $slim = null)
	{
		$this->app = !empty($slim) ? $slim : \Slim\Slim::getInstance();
		if (!empty($slim)) {
			if (! self::userTableExists()) { // Was: 'blah'
				exit;
			}

		  // Was: $this->setup();
		}
	}

  public static function databaseExists()
	{
		try {
			$_db = ORM::get_db();
		}
		catch (\PDOException $ex) {
			self::dberror( $ex, 'Database error. Database does not exist.', 'Database warning. Database does not exist.' );
			return false;
		}
		return true;
	}

	public static function userTableExists( $table = 'users' )
	{
		try {
			$user = ORM::for_table( $table )
					->where_equal( 'username' , 'admin' )
					->find_one();
		}
		catch (\PDOException $ex) {
			self::dberror( $ex, "Database warning. Table '$table' does NOT exist, or is NOT seeded." );

			// $this->app->flashKeep('error', 'Database error');
			// $this->app->error();
			return false;
		}
		if (CLI) {
			echo "Database OK. Table '$table' does exist.\n";
		} else {
			self::_debug([ __METHOD__, "DB OK. Table '$table' DOES exist." ]);
			// Was: echo "<!-- // DB OK. Table '$table' does exist. -->";
		}

		return true;
	}

	public static function config( $key, $var = 'openessayist_config' )
	{
		switch ($var) {
			case 'db':
				$grp = $GLOBALS[ 'activeGroup' ];
				return isset($GLOBALS[ 'db' ][ $grp ][ $key ]) ? $GLOBALS[ 'db' ][ $grp ][ $key ] : null;
				break;
			case 'openessayist_config':
				return isset($GLOBALS[ $var ][ $key ]) ? $GLOBALS[ $var ][ $key ] : null;
				break;
		}
		return false;
	}

	/** LEGACY.
	 */
	public function setup($reset = false)
	{
		$seed_db = $GLOBALS[ 'seedDatabase' ];
		$email = $GLOBALS[ 'email' ];

		self::_debug([ __METHOD__, $email, 'seed DB?', $seed_db, $reset ]);

		//$log = $this->app->getLog();
		//$log->debug("SETUP PROCEDURE CALLED");

		// Create .logs dir if it does not exists
		if (!is_dir('../.logs')) {
			mkdir('../.logs');
		}

		// check DB
		$this->db = ORM::get_db();

    // ...
	}

	protected function csv_to_array($input, $delimiter=',',$header=null)
	{
		//$header = null;
		$data = array();
		$csvData = str_getcsv($input, "\n");

		foreach($csvData as $csvLine){
			if(is_null($header)) {
				$header = explode($delimiter, $csvLine);
			}
			else {

				$items = explode($delimiter, $csvLine);

				for($n = 0, $m = count($header); $n < $m; $n++){
					$prepareData[$header[$n]] = $items[$n];
				}

				$data[] = $prepareData;
			}
		}

		return $data;
	}


	public function run()
	{
		$this->app->run();
	}

	// ------------------------------------------------------------------------

	/** Output a HTTP header with debug information.
	 * @param mixed $obj  Object (stdClass), array, string etc.
	 * @return void
	 */
	public static function _debug( $obj ) {
		static $count = 0;
		header(sprintf( 'X-app-%02d: %s', $count, json_encode( $obj )));
		$count++;
	}

	protected static function dberror(\PDOException $ex, $message = 'Database error.', $cli_message = null) {
		header( 'HTTP/1.1 503 Service Unavailable' );

		if (defined( 'CLI' ) && CLI) {
			fwrite( STDERR, ( $cli_message ? $cli_message : $message ) . PHP_EOL);
			fwrite( STDERR, $ex->getMessage() );
		} else {
			?><!doctype html><title>Database error</title><style> body { font: 1em sans-serif; margin: 3em auto; text-align: center; } </style>
			<p> <?= $message ?> </p>
			<?php
			echo $ex->getMessage();

			// $this->app->error('error');
		}
	}

	protected static $once = false;

	protected function debugInit($caller) {
		static $shown = false;
		if (! self::$once) {
		//if (! $shown) {
			$auth = $this->auth;
			self::_debug([ __METHOD__, $caller, get_class( $auth ), $auth->getUser(), $this->getAnalyserUrl(), $this->getSavePath() ]);
		}
		self::$once = true;
		// $shown = true;
	}

  protected function getSavePath() {
		return realpath(self::config( 'rd_save_path' )); // Was: $this->app->config('rd_save_path')
	}

  /**
	 * @param string $path  Path part of desired URL for the pyEssayAnalyser service.
	 * @return string  URL
	 */
	protected function getAnalyserUrl($path = '/') {
		$analyser_url = self::config( 'analyser_url' ); // Was: $GLOBALS[ 'analyserUrl' ]
		return ( $analyser_url ? $analyser_url : 'http://localhost:8062' ) . $path;
	}
}
