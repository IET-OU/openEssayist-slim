<?php
/**
 * OpenEssayist front controller.
 *
 * @package   OpenEssayist-slim
 * @copyright © 2013-2018 The Open University. (Institute of Educational Technology)
 */

require_once __DIR__ . '/../app/utils/autoload.php';

use Slim\Slim;
use Slim\Extras\Views\Twig as TwigView;
use Slim\Extras\Middleware\StrongAuth;
use Slim\Extras\Log\DateTimeFileWriter;
use IET_OU\OpenEssayist\Utils\LoggerMiddleware; // Was: \Slim\Middleware\LoggerMiddleWare;
use IET_OU\OpenEssayist\Utils\StrongAuthAdmin;  // Was: \Slim\Extras\Middleware\StrongAuthAdmin;

IET_OU\OpenEssayist\Utils\DBConnection::dbconnect();

if (! Application::databaseExists()) {
	exit;
}

// \Slim\Slim::registerAutoloader();

// Create main Slim application
$app = new \Slim\Slim(array(
	'openEssayist.async' => false,
	'view' => new TwigView,
	'debug' => true,
	'rd_save_path' => Application::config( 'rd_save_path' ),
	// Was: 'rd_save_path' => substr($_SERVER['SCRIPT_FILENAME'], 0, -21) .'public_html/assets/openessayist/img/rd/',
    'log.level' => \Slim\Log::DEBUG,
    'log.enabled' => true,
    'log.writer' => new DateTimeFileWriter(array(
        'path' => __DIR__ . '/../.logs',
        // Was: 'path' => '../.logs',
        'name_format' => 'Y-m-d',   // 4-digit year! Was: 'y-m-d',
    	'message_format' => '%label% | %date% | %message%'
    ))
));

// $app->log->setEnabled(true);


// Twig Asset Management.
// Set custom Twig filters -- code moved to `app/utils/TwigApp.php`.

$twigApp = new IET_OU\OpenEssayist\Utils\TwigApp( $app->view() );
// Was: IET_OU\OpenEssayist\Utils\TwigApp::setup( $app->view() );


// Create a hook to add the root URI to all views
$app->hook('slim.before.dispatch', function() use ($app) {
	$app->view()->appendData(array(
			'app_base' 	=> $app->request()->getRootUri(),
			'assets' 		=> $app->request()->getRootUri() . '/assets/openessayist/',
			'img' 		  => $app->request()->getRootUri() . '/assets/openessayist/img/',
			'js' 				=> $app->request()->getRootUri() . '/assets/openessayist/js/',
			'user_img'	=> $app->request()->getRootUri() . '/user-images/',
	));
});

/**
 * @var $config
 * Configuration for Idiom & StrongAuth
 */
$config = array(
		'provider' => 'PDOAdmin',
		'pdo' => ORM::get_db(),
		'auth.type' => 'form',				// identification by form
		'login.url' => '/login',			// URL for login form
		'consent.url' => '/me/consent',		// URL for consent form
		'security.urls' => array(			// URLs for secured access
				array('path' => '/account/'),
				array('path' => '/tutor/.+'),
				array('path' => '/me/'),
				array('path' => '/me/.+'),
				array('path' => '/group/'),
				array('path' => '/group/.+'),
				array('path' => '/admin/', 'admin'=> true),
				array('path' => '/admin/.+', 'admin' => true),
		),
);

// Was: ORM::configure('logging', true);

// Define and add the StrongAuth middleware to the framework
$app->add(new StrongAuthAdmin($config, new Strong\Strong($config)));
$app->add(new LoggerMiddleWare());

// Create the openEssayist application core
$coreWebApp = new Application($app);

\IET_OU\OpenEssayist\Utils\defineRoutes($coreWebApp);

// Run the application.
$coreWebApp->run();

// End.
