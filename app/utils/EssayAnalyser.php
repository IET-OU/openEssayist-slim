<?php namespace IET_OU\OpenEssayist\Utils;

/**
 * PHP wrapper round Python EssayAnalyser service.
 *
 * @package   OpenEssayist-slim
 * @copyright © 2013-2018 The Open University. (Institute of Educational Technology)
 * @author    Nick Freear, 02-February-2018.
 */

class EssayAnalyser extends \Application
{
  /**
   * @return object  Return an initialized 'result' object.
   */
  public function initResult()
  {
    return (object) [
      'status' => 200,
      'redirect' => false,
      'resp' => null,  // Was: 'r'.
      'db_result' => null,
    ];
  }

  /**
   * Call the analyse service, and save the analysis to the database.
   *
   * @param int $taskId
   * @param int $userId
   * @return object  A 'result' object.
   */
  public function analyseAndSave( $taskId, $userId )
  {
    $req = $this->app->request();
    $log = $this->app->getLog();
    $post = $req->post();

    $result = $this->initResult();

    $url = $this->getAnalyserUrl('/api/analysis');

    $versionId = $post[ 'version' ];

    $post_data = [
        'text' 				=> $post["text"],
        'module' 			=> $post["module"],  // 'module' = 'group.code' = 'H810'.
        'task' 				=> $post["task"],    // 'task' = 'task.code' = 'TMA01'.
        'task_id' 		=> $taskId,
        'version_id' 	=> $versionId,
        'user_id' 		=> $userId, // Was: $this->user['id'],
        'rd_save_path'=> $this->getSavePath(),
    ];

    $log->debug(__METHOD__ . ":start - $taskId,$versionId - " . date('c'));

    $times = (object) [ 'start' => time() ];

    $request = \Requests::post($url,
        array(),
        $post_data,
        array(
            'timeout' => 5 * 60, // Was: 300.
            'blocking' => true
    ));

    $times->end = time();

    $times->duration = $times->end - $times->start;

    $log->debug(__METHOD__ . ":end - $taskId,$versionId - seconds:" . $times->duration);

    $post_data[ 'text' ] = substr( $post_data[ 'text' ], 0, 30 ) . ' [...]';
    self::_debug([ __METHOD__, 'POST', $post_data ]);

    if ($request->status_code === 200)
    {
      $json = $request->body;
      $ret = json_decode($json, true);

      /* @var $draft Draft */

      $result->db_result = $this->saveDraft( $taskId, $userId, $post, $times, $json );
      // Was: $result = $draft->save();

      // redirect to the "drafts review" page
      $this->app->flash('info', 'The analysis of your draft was successful. Check the details below.');
      $result->resp = $this->app->urlFor("me.draft.action", array("idt" => $taskId));

      self::_debug([ 'm' => __METHOD__, 'ok', 'u' => $url, 'taskId' => $taskId, 'draftVersion' => $versionId, 'result' => $result, 'duration_sec' => $times->duration, 'counts' => $counts ]);

      $result->status = 200;
      $result->redirect = true;
      // Was: $this->redirect($r, false);
    }
    else
    {	$json = $request->body;
      $ret = json_decode($json, true);

      $result->status = 500;

      $this->app->flashNow("error", "Sorry. Problem with the analyser. Make sure you text is not empty. If it continues, please contact the admin.");

      self::_debug([ __METHOD__, 'error', 500.1, $ret ]);
    }

    return $result;
  }

  /**
   * Create a Draft model object, and save to the database.
   * @param int $taskId
   * @param int $userId
   * @param array $post Post-data from the end-user.
   * @param object $times
   * @param string $analysisJson
   */
  protected function saveDraft( $taskId, $userId, $post, $times, $analysisJson )
  {
    /* @var $draft Draft */
    $draft = Model::factory('Draft')->create();
    $draft->type = 0;
    $draft->analysis = $analysisJson;  // Was: $json;
    $draft->task_id = $taskId;
    $draft->version = $post["version"];
    $draft->name = $post["name"];
    $draft->users_id = $userId;  // Was: $this->user['id'];
    $draft->date = date('Y-m-d H:i:s');  // No timezone ?!
    // Was: $draft->date = date('Y-m-d H:i:s e');

    $draft->text = $post[ 'text' ]; // The original text.
    $draft->tstart = $times->start;
    $draft->tend = $times->end;

    $counts = json_encode($post[ 'counts' ]);  // JSON via Countable.js.
    $draft->counts = json_decode( $counts );

    $db_result = $draft->save();

    return $db_result;
  }
}
