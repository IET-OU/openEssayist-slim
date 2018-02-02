<?php
/**
 * Fake autoloading for now!
 *
 * @package   OpenEssayist-slim
 * @copyright © 2013-2018 The Open University. (Institute of Educational Technology)
 */

header( 'X-test: 1' );

error_reporting( E_ALL );
ini_set( 'display_errors', true );

require_once __DIR__ . '/../../vendor/autoload.php';

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../application.php';

// Utilities.
require_once __DIR__ . '/../utils/DBConnection.php';
require_once __DIR__ . '/../utils/CliApp.php';
require_once __DIR__ . '/../utils/AnalysisUtils.php';
require_once __DIR__ . '/../utils/LoggerMiddleware.php';
require_once __DIR__ . '/../utils/PDOAdmin.php';
require_once __DIR__ . '/../utils/StrongAuthAdmin.php';
require_once __DIR__ . '/../utils/TwigApp.php';
require_once __DIR__ . '/../utils/UASparser.php';

// Controllers.
require_once __DIR__ . '/../controller.php';
require_once __DIR__ . '/../controllers/admin.controller.php';
require_once __DIR__ . '/../controllers/home.controller.php';
require_once __DIR__ . '/../controllers/login.controller.php';
require_once __DIR__ . '/../controllers/user.controller.php';
require_once __DIR__ . '/../controllers/demo.controller.php';
require_once __DIR__ . '/../controllers/tutor.controller.php';
require_once __DIR__ . '/../controllers/group.controller.php';
require_once __DIR__ . '/../controllers/uptime.controller.php';

// Models (X 7) - multiple models per file :(!
require_once __DIR__ . '/../models/users.model.php';
require_once __DIR__ . '/../models/draft.model.php';

// System's constants
define('APPLICATION', 'openEssayist');
define('VERSION', '2.7 beta');
define('EXT', '.twig');

// End.
