<?php namespace IET_OU\OpenEssayist\Utils;

/**
 * Setup the TWIG template environment.
 *
 * @package   OpenEssayist-slim
 * @copyright © 2013-2018 The Open University. (Institute of Educational Technology)
 * @author    Nick Freear, 02-February-2018.
 */

// Was in:  `public_html/index.php`

use \Slim\Extras\Views\Twig as TwigView;
use IET_OU\OpenEssayist\Utils\VersionedAsset;

class TwigApp
{
  protected $twig;

  protected $resource;

  public function __construct( $view )
  {
    $this->resource = new VersionedAsset();

    $this->setup( $view );
  }

  /**
   * Configure extensions, template directory, setup custom filters.
   * @param object $view
   */
  protected function setup( $view )
  {
    // Asset Management
    TwigView::$twigExtensions = array(
    	'Twig_Extensions_Slim',
    	'Twig_Extension_Debug'
    );

    TwigView::$twigTemplateDirs = array(
    	__DIR__ . '/../../templates',  // Was: '../templates',
    );

    TwigView::$twigOptions = array(
    	'cache' => __DIR__ . '/../../.cache', // Was: '../.cache',
    	'debug'=> true,
    );

    if ($view instanceof TwigView)
    {
    	/* @var $twig Twig_Environment */
      $this->twig = $view->getEnvironment();

      $this->createFilterFunctions();

      $this->createTestFunctions();
    }
  }

  protected function createFilterFunctions()
  {
    /**
     * Create a TWIG filter for Boolean values
     * @param 	$var	The variable to render
     * @return	A String containing "True" or "False"
     * Usage: {{ item|boolean}}
     */
    $this->twig->addFilter(new \Twig_SimpleFilter('boolean', function ($var) {
      if (is_bool($var)) {
        return ($var) ? 'True' : 'False';
      } else {
        return $var;
      }
    }));

    /**
     * A TWIG filter for configuration values. USAGE: {{ 'my_config_var' | config }}
     * @param  string $key
     * @return string String (or object) value.
     */
    $this->twig->addFilter(new \Twig_SimpleFilter('config', function ($key) {
      return \Application::config($key);
    }));

    /**
     * TWIG filter to get a Javascript URL. USAGE: {{ 'jquery' | javascript }}
     * @return string
     */
    $this->twig->addFilter(new \Twig_SimpleFilter('javascript', function ($key) {
      return $this->resource->getJavascriptUrl( $key );
    }));

    /**
     * TWIG filter to get a stylesheet URL. USAGE: {{ 'twitter-bootstrap' | stylesheet }}
     * @return string
     */
    $this->twig->addFilter(new \Twig_SimpleFilter('stylesheet', function ($key) {
      return $this->resource->getStylesheetUrl( $key );
    }));
  }

  protected function createTestFunctions()
  {
    /**
     * Create a TWIG test for checking the existence of a value in an array
     * @param 	$val	The value to search for
     * @param 	$arr	The array
     * @param 	$def	The default value if the array does not exist
     * @return	True if the value is in the array, False if not, $def if the array is not set
     * Usage: {{ val is inOption(arr,def) }}
     */
    $this->twig->addTest(new \Twig_SimpleTest('inOption', function ($val, $arr, $def = true) {
      if (! isset($arr)) {
        return $def;
      }
      if (isset($arr) && in_array($val, $arr) ) {
        return true;
      }
      return false;
    }));
  }
}
