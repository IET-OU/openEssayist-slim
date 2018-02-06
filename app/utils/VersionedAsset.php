<?php namespace IET_OU\OpenEssayist\Utils;

/**
 * Versioned URLs for Javascript and CSS stylesheets - including 3rd-party - Later via CDN.
 *
 * @package   OpenEssayist-slim
 * @copyright © 2013-2018 The Open University. (Institute of Educational Technology)
 * @author    Nick Freear, '3 Feb 00:29', 2-3 February 2018.
 */

class VersionedAsset extends \Application {
// Was: class ResourceVersion extends \Application {

  const PACKAGE_JSON = __DIR__ . '/../../package.json';

  // Note, the key eg. 'font-awesome' matches a 'peerDependency' in 'package.json'.
  protected $styles = [
    'x-google-droid-sans' => 'https://fonts.googleapis.com/css?family=Droid+Sans:400,700',
    'font-awesome' => 'https://unpkg.com/font-awesome@{ver}/css/font-awesome.min.css',
    'twitter-bootstrap' => 'https://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css',
    'bootstrap-modal' => 'https://cdn.rawgit.com/jschr/bootstrap-modal/{ver}/css/bootstrap-modal.css',
    'pnotify' => '{assets}/jquery.pnotify/jquery.pnotify.default.css',
    '_X_CDN_pnotify' => 'https://unpkg.com/pnotify@{ver}/dist/pnotify.css',
  ];

  protected $scripts = [
    'twitter-bootstrap' => 'https://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js',
    'bootstrap-modal' => 'https://cdn.rawgit.com/jschr/bootstrap-modal/{ver}/js/bootstrap-modalmanager.js',
    'pnotify' => '{assets}/jquery.pnotify/jquery.pnotify.js',
    '_X_CDN_pnotify' => 'https://unpkg.com/pnotify@{ver}/dist/pnotify.js',
    'jquery' => 'https://unpkg.com/jquery@{ver}/dist/jquery.min.js',
    'jqueryui' => 'https://unpkg.com/jqueryui@{ver}/jquery-ui.min.js',
    'jquery-dotdotdot' => '{assets}/dotdotdot-1.6.3/jquery.dotdotdot.min.js',
    '_X_CDN_jquery-dotdotdot' => 'https://unpkg.com/jquery-dotdotdot@{ver}/src/jquery.dotdotdot.min.js',
    'jquery-blockui' => '{assets}/jquery-ui-1.9.2.custom/js/jquery.blockUI.js', // X 7;
    '_X_LOCAL_d3' => '{assets}/d3.js/d3.v3.min.js',
    'd3' => 'https://unpkg.com/d3@{ver}/d3.min.js', // X 10;
    'd3-colorbrewer' => '{assets}/d3.js/colorbrewer.js', // X 10;
    // 'highcharts'
    'datatables' => '{assets}/jquery.dataTables/js/jquery.dataTables.min.js',
    '_X_CDN_datatables' => 'https://unpkg.com/datatables@1.10.13/media/js/jquery.dataTables.min.js',
    'countable' => 'https://unpkg.com/countable@{ver}/Countable.min.js',
    'x-exhibit-api' => 'https://api.simile-widgets.org/exhibit/3.0.0/exhibit-api.js',
  ];

  protected static $depends = [];

  public function __construct($load_package = true)
  {
    parent::__construct();

    if ($load_package) {
      self::loadPackageJson( self::PACKAGE_JSON );
    }
  }

  /**
   * @param string $key  Get the URL for a Javascript by key, eg. 'jquery'.
   * @return string
   */
  public function getJavascriptUrl( $key )
  {
    // self::_debug([ __METHOD__, $key, $this->scripts ]);
    return strtr( $this->scripts[ $key ], $this->getReplace( $key )) . '#-VA-0';
  }

  /**
   * @param string $key  Get the URL for a stylesheet by key, eg. 'twitter-bootstrap'.
   * @return string
   */
  public function getStylesheetUrl( $key )
  {
    return strtr( $this->styles[ $key ], $this->getReplace( $key )) . '#-VA-0';
  }

  protected function getReplace( $key )
  {
    return [
      '{assets}' => $this->app->request()->getRootUri() . '/assets',
      '{ver}' => isset(self::$depends->{ $key }) ? self::$depends->{ $key } : '[none]',
    ];
  }

  protected static function loadPackageJson( $filename )
  {
    $pkg = json_decode(file_get_contents( $filename ));

    self::$depends = $pkg->peerDependencies;

    self::_debug([ __METHOD__, $pkg->name ]);
  }
}
