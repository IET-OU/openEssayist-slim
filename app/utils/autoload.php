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

require_once __DIR__ . '/../../config/app.php';  // Was: __DIR__ . '/../config.php';
require_once __DIR__ . '/../application.php';

// Utilities.
require_once __DIR__ . '/../utils/DBConnection.php';
require_once __DIR__ . '/../utils/CliApp.php';
require_once __DIR__ . '/../utils/EssayAnalyser.php';
require_once __DIR__ . '/../utils/AnalysisUtils.php';
require_once __DIR__ . '/../utils/LoggerMiddleware.php';
require_once __DIR__ . '/../utils/PDOAdmin.php';
require_once __DIR__ . '/../utils/StrongAuthAdmin.php';
require_once __DIR__ . '/../utils/TwigApp.php';
require_once __DIR__ . '/../utils/UASparser.php';
require_once __DIR__ . '/../utils/VersionedAsset.php';

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

// Models (X 7).
require_once __DIR__ . '/../models/draft.model.php';
require_once __DIR__ . '/../models/Feedback.php';
require_once __DIR__ . '/../models/Group.php';
require_once __DIR__ . '/../models/KWCategory.php';
require_once __DIR__ . '/../models/Note.php';
require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/users.model.php';

// Routes.
require_once __DIR__ . '/../utils/routes.php';

// System's constants
define('APPLICATION', 'openEssayist');
define('VERSION', '3.0 beta');
define('EXT', '.twig');

// End.
