<?php namespace IET_OU\OpenEssayist\Utils;

/**
 * Setup the TWIG template environment.
 *
 * @package   OpenEssayist-slim
 * @copyright © 2013-2018 The Open University. (Institute of Educational Technology)
 * @author    Nick Freear, 02-February-2018.
 */

// Was in:  `public_html/index.php`

class TwigApp
{
  /**
   * Configure extensions, template directory, setup custom filters.
   * @param object $view
   */
  public static function setup( $view )
  {
    // Asset Management
    \TwigView::$twigExtensions = array(
    	'Twig_Extensions_Slim',
    	'Twig_Extension_Debug'
    );

    \TwigView::$twigTemplateDirs = array(
    	__DIR__ . '/../../templates',  // Was: '../templates',
    );

    \TwigView::$twigOptions = array(
    	'cache' => __DIR__ . '/../../.cache', // Was: '../.cache',
    	'debug'=> true,
    );

    if ($view instanceof \TwigView)
    {
    	/* @var $twig Twig_Environment */
    	$twig = $view->getEnvironment();

      $filters = self::createFilterFunctions();

      $twig->addFilter($filters->boolean);
    	$twig->addFilter($filter->config);

    	$twig->addTest(self::createTestFunctions());
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
    $boolean_filter = new \Twig_SimpleFilter('boolean', function ($var) {
      if (is_bool($var)) {
        return ($var) ? 'True' : 'False';
      } else {
        return $var;
      }
    });

    /**
     * A TWIG filter for configuration values. USAGE: {{ 'key' | config }}
     * @param  string $key
     * @return string String (or object) value.
     */
    $config_filter = new \Twig_SimpleFilter('config', function ($key) {
      return \Application::config($key);
    });

    return (object) [
      'boolean' => $boolean_filter,
      'config' => $config_filter,
    ];
  }

  protected static function createTestFunctions()
  {
    /**
     * Create a TWIG test for checking the existence of a value in an array
     * @param 	$val	The value to search for
     * @param 	$arr	The array
     * @param 	$def	The default value if the array does not exist
     * @return	True if the value is in the array, False if not, $def if the array is not set
     * Usage: {{ val is inOption(arr,def) }}
     */
    $test = new \Twig_SimpleTest('inOption', function ($val, $arr, $def = true) {
      if (! isset($arr)) {
        return $def;
      }
      if (isset($arr) && in_array($val, $arr) ) {
        return true;
      }
      return false;
    });

    return $test;
  }
}
