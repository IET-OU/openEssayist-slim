<?php
/**
 * Status / Uptime controller. OpenEssayist-slim.
 *
 * @copyright © 2013-2018 The Open University. (Institute of Educational Technology)
 * @author    Nick Freear, 24-Jan-2018.
 */

class UptimeController extends Controller
{
  const VERSION_JSON = __DIR__ . '/../../public_html/version.json';

  public function backend()
	{
		$http_status = '500 Internal Server Error';
		$response = $message = $raw = $success = null;
		$homeUrl = $this->app->request()->getRootUri() . '/';

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

    /* $response->body = $response->raw = '[ ... ]';
		var_dump( $response ); exit; */

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

		// $this->app->response->setStatus( $http_status );
		header( 'HTTP/1.1 ' . $http_status );
		self::_debug([ __METHOD__, 'http_status', $http_status, $raw, $this->getAnalyserUrl() ]);

    self::printVersionData($headers = true);
?>
<!doctype html>
<html class="<?= $success ? 'ok' : 'error' ?>" data-stat="<?= $http_status ?>" data-raw="<?= $raw ?>" lang="en">
	<meta name="robots" content="noindex">
	<title> OpenEssayist - status (uptime) </title>
	<style> body { font: 1em sans-serif; margin: 3em auto; min-width: 21em; } p { text-align: center; } .error .m { color: #d00; } </style>
	<p class="m"> <?= $message ?> </p>

	<p> <a href="<?= $homeUrl ?>">openEssayist home</a> </p>
	<p role="contentinfo"><small> <a href="http://www.open.ac.uk/">©The Open University</a> </small></p>

	<?php self::printVersionData() ?>
</html>
<?php
    exit;
	}

  protected static function printVersionData($headers = false)
  {
    $backend_file = self::config('analyser_version');
    $version_frontend = file_get_contents(self::VERSION_JSON);
    $version_backend = $backend_file ? file_get_contents($backend_file) : json_encode([ 'stat' => 'missing' ]);

    if ($headers):
      self::_debug([ 'OpenEssayist-ver' => $version_frontend ]);
      self::_debug([ 'EssayAnalyser-ver' => $version_backend ]);
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
