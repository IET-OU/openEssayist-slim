<?php
/**
 * OpenEssayist-slim.
 *
 * @copyright © 2013-2018 The Open University. (Institute of Educational Technology)
 * @author Nicolas Van Labeke (https://github.com/vanch3d)
 */

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
			$this->setup();
		}
	}

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

		if ($reset)
		{
			$this->db->exec('DROP DATABASE `openessayist`');
			$this->db->exec('CREATE DATABASE `openessayist`');
		}

		// Create Users Table
		try {
			$ret = $this->db->exec("
				CREATE TABLE `users` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `username` varchar(120) DEFAULT NULL,
				  `password` varchar(255) NOT NULL,
				  `name` varchar(180) DEFAULT NULL,
				  `email` varchar(220) DEFAULT NULL,
				  `ip_address` varchar(16) NOT NULL,
				  `group_id` int(11) NOT NULL,
				  `active` int(11) DEFAULT '0',
				  `isadmin` int(11) DEFAULT '0',
				  `isgroup` int(11) DEFAULT '0',
				  `isdemo` int(11) DEFAULT '0',
				  PRIMARY KEY (`id`),
				  UNIQUE (`username`)
				) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
			");
		}
		catch (\PDOException $e)
		{
			// Table exists, assume all the rest is fine
			return;
		}

		// Group Table
		$this->db->exec("
			CREATE TABLE IF NOT EXISTS `group` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(120) DEFAULT NULL,
			  `code` varchar(120) DEFAULT NULL,
			  `url` varchar(255) DEFAULT NULL,
			  `email` varchar(220) DEFAULT NULL,
			  `description` TEXT,
			  PRIMARY KEY (`id`),
			  UNIQUE (`name`)
			) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
		");

		// Task Table
		$this->db->exec("
			CREATE TABLE IF NOT EXISTS `task` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`name` varchar(120) DEFAULT NULL,
			    `code` varchar(120) DEFAULT NULL,
				`assignment` TEXT,
				`deadline` DATE,
				`wordcount` int(11) DEFAULT '0',
				`isopen` int(11) DEFAULT '0',
				`group_id` int(11) NOT NULL,
				PRIMARY KEY (`id`)
			)  AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
		");
		//var_dump("Table 'task' created");

		// Draft Table
		$this->db->exec("
			CREATE TABLE IF NOT EXISTS `draft` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`task_id` int(11) NOT NULL,
				`users_id` int(11) NOT NULL,
				`type` int(11) DEFAULT '0',
				`processed` int(11) DEFAULT '1',
				`version` int(11) NOT NULL DEFAULT '1',
				`name` varchar(120) DEFAULT NULL,
				`analysis` LONGBLOB,
				`date` DATETIME,
				PRIMARY KEY (`id`)
			)  AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
		");

		// Draft Table
		$this->db->exec("
			CREATE TABLE IF NOT EXISTS `kwcategory` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`draft_id` int(11) NOT NULL,
				`category` LONGBLOB,
				PRIMARY KEY (`id`)
			)  AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
		");

		// Notes Table
		$this->db->exec("
			CREATE TABLE IF NOT EXISTS `note` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`users_id` int(11) NOT NULL,
				`notes` LONGBLOB,
				PRIMARY KEY (`id`)
			)  AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
		");

		// Feedback Table
		$this->db->exec("
			CREATE TABLE IF NOT EXISTS `feedback` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`users_id` int(11) NOT NULL,
			    `referer` varchar(255) DEFAULT NULL,
				`text` TEXT,
				`date` DATETIME,
				PRIMARY KEY (`id`)
			)  AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
		");

		$exist = file_exists('../app/localconfig.php');
		if ($exist && $seed_db)
		{
			//$log->debug("LOCAL SETUP INVOKED");
			include '../app/localconfig.php';
			$conf = new OpenEssayistConfigurator();
			$conf->setupDB();
		}
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

	protected static $once = false;

	protected function debugInit($caller) {
		static $shown = false;
		if (! self::$once) {
		//if (! $shown) {
			$auth = $this->auth;
			self::_debug([ __METHOD__, $caller, get_class( $auth ), $auth->getUser(), $this->getAnalyserUrl(), $this->getSavePath() ]);
		}
		self::$once = true;
		//$shown = true;
	}

  protected function getSavePath() {
		return $this->app->config('rd_save_path');
	}

  /**
	 * @param string $path  Path part of desired URL for the pyEssayAnalyser service.
	 * @return string  URL
	 */
	protected function getAnalyserUrl($path = '/') {
		return ( isset($GLOBALS[ 'analyserUrl' ]) ? $GLOBALS[ 'analyserUrl' ] : 'http://localhost:8062' ) . $path;
	}
}
