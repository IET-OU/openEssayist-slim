<?php
/**
 * Status / Uptime controller - checks the backend service.
 *
 * @package   OpenEssayist-slim
 * @copyright © 2013-2018 The Open University. (Institute of Educational Technology)
 * @author    Nick Freear, 24-Jan-2018.
 */

class UptimeController extends Controller
{
  const VERSION_JSON = __DIR__ . '/../../public_html/version.json';

  /** @route "/status".
   */
  public function status()
	{
		$http_status = '500 Internal Server Error';
		$response = $message = $raw = $success = null;

		try {
		  $response = Requests::get($this->getAnalyserUrl(), [ 'timeout' => 300, 'blocking' => true ]);
	  }
		catch (Requests_Exception $ex) {
			$success = false;
			$http_status = '502 Bad Gateway'; // '503 Service Unavailable';
			$message = $raw = $ex->getMessage();

			if (preg_match( '/.+Connection refused/i', $raw )) {
			  $message = 'Analyser error: backend service is down.';
		  }
		}
		catch (Exception $ex) {
			$success = false;
			$message = $raw = $ex->getMessage();
		}

		if ($response) {
			$success = $response->success; // $response->status_code === 200;
			$http_status = $response->status_code;

		  if ($response->status_code === 200):
				$http_status = '200 OK';
			  $message = $raw = 'Analyser backend is OK!';
		  else:
			  $message = $raw = 'Analyser error. HTTP status: ' . $response->status_code;
		  endif;
	  }

    $this->renderStatusPage( $success, $http_status, $message, $raw );
  }

  /** Render the status page.
   */
  protected function renderStatusPage( $success, $http_status, $message, $raw ) {
    $baseUrl = $this->app->request()->getRootUri() . '/';

		// $this->app->response->setStatus( $http_status );
		header( 'HTTP/1.1 ' . $http_status );
		self::_debug([ __METHOD__, 'http_status', $http_status, $raw, $this->getAnalyserUrl() ]);

    self::printVersionData($headers = true);
?>
<!doctype html>
<html class="<?= $success ? 'ok' : 'error' ?>" data-stat="<?= $http_status ?>" data-raw="<?= $raw ?>" lang="en">
	<meta name="robots" content="noindex">
	<title> OpenEssayist — status (uptime) </title>
	<style> body { background: #fefefe; font: 1em sans-serif; margin: 3em auto; max-width: 45em; min-width: 20em; text-align: center; }
  .m{ padding:1em; border:1px solid; } .error .m{ background:#fee; color:#d00; } .ok .m{ background:#dfd; border-color:#bdb; color:#050; } .f{ margin:4em; padding:1em; border-top:1px solid #bbb; } </style>
	<link href="<?= $baseUrl ?>assets/openessayist/img/favicon.ico" rel="shortcut icon">

	<p class="m"> <?= $message ?> <small>(<?= $http_status ?>)</small> </p>

	<p> <a href="<?= $baseUrl ?>">openEssayist home</a> </p>
	<p role="contentinfo" class="f"><small> <a href="http://www.open.ac.uk/">©The Open University</a> </small></p>

	<?php self::printVersionData() ?>
</html>
<?php
    exit;
	}

  protected static function printVersionData($headers = false)
  {
    $backend_file = self::config('analyser_version');
    $missing = json_encode([ 'stat' => 'missing' ]);

    $version_frontend = file_exists(self::VERSION_JSON) ? file_get_contents(self::VERSION_JSON) : $missing;
    $version_backend = file_exists($backend_file) ? file_get_contents($backend_file) : $missing;

    if ($headers):
      self::_debug([ 'OpenEssayist-ver' => json_decode($version_frontend) ]);
      self::_debug([ 'EssayAnalyser-ver' => json_decode($version_backend) ]);
    else: ?>

    <script id="openessayist-ver"  type="application/json"> <?= $version_frontend ?> </script>
    <script id="essayanalyser-ver" type="application/json"> <?= $version_backend ?> </script>
<?php
  endif;
  }

	private function _x_demoGroupTexts($draft)
	{
		$dr = $this->getDraft($draft);
		$tsk = $dr->task()->find_one();

		// ...

		$this->render('drafts/group.texts',array(
				'task' => $tsk->as_array(),
				'draft' => $dr->as_array()
		));
	}
}
