<?php namespace IET_OU\OpenEssayist\Utils;

/**
 * Post-analysis utilities for OpenEssayist.
 *
 * @package   OpenEssayist-slim
 * @copyright © 2013-2018 The Open University. (Institute of Educational Technology)
 */

class AnalysisUtils {

  /**
  * @return array
  */
  public static function GetStructureData($id = null)
  {
    $arr = array(
      '#dummy#'	=> array('name' => 'Unrecognised', 'idx' => '8'),
      '#+s:c#'	=> array('name' => 'Conclusion', 'idx' => '1'),
      '#+s:d_i#'  => array('name' => 'Discussion', 'idx' => '2'),
      '#+s:d#'	=> array('name' => 'Discussion', 'idx' => '3'),
      '#+s#'		=> array('name' => 'Discussion', 'idx' => '3'),
      '#+s:s#'	=> array('name' => 'Summary', 'idx' => '4'),
      '#+s:i#'	=> array('name' => 'Introduction', 'idx' => '5'),
      '#-s:t#'	=> array('name' => 'Title', 'idx' => '6'),
      '#+s:p#'	=> array('name' => 'Preface', 'idx' => '7'),
      '#-s:h#'	=> array('name' => 'Heading', 'idx' => '8'),
      '#-s:n#'	=> array('name' => 'Numerics', 'idx' => '9'),
      '#-s:q#'	=> array('name' => 'Assignment', 'idx' => '10'),
      '#-s:p#'	=> array('name' => 'Punctuation', 'idx' => '11'),
    );
    if ($id) {
      return $arr[$id];
    }
    else {
      return $arr;
    }
  }

  /**
   * @param object $analysis.
   * @param object $userdata  KWCategory model.
   * @return object|null  StdClass object containing 'allkw' and 'groups' properties.
   */
  public static function GetAllKeywords($analysis, $userdata)
  {
    if (! $analysis) {
      return null;
    }

    // Get all ngrams in a single structure
    $data = array_merge(array(),
        $analysis->nvl_data->quadgrams,
        $analysis->nvl_data->trigrams,
        $analysis->nvl_data->bigrams,
        $analysis->nvl_data->keywords
    );

    $allkw = array();
    // Transform into associative array
    foreach ($data as $ngram)
    {
      $id = join('', $ngram->ngram);
      $allkw[$id] = $ngram;
    }

    // Get the user-defined keywords
    // Get the groups
    $groups = null;
    if ($userdata != false)
    {
      $groups = $userdata->getGroups();
    }

    if ($groups == null)
    {
      $kw = array();
      foreach ($allkw as $key => $item)
      {
        //$item->groupid = 'category_all';
        $kw[] = $key;
      }
      $groups = array('category_all' =>
          array('id' => 'category_all', 'keywords' => $kw));
    }

    foreach ($groups as $gr)
    {
      if (isset($gr['keywords'])) {
        foreach ($gr['keywords'] as $keyw) {
          $allkw[$keyw]->groupid = $gr['id'];
        }
      }
    }

    $ret = new \stdClass();
    $ret->allkw = $allkw;
    $ret->groups = $groups;

    return $ret;
  }

  /**
   * @param object $draft  (NOT used!)
   * @param object $dr   Draft model.
   * @param object $tsk  Task object.
   * @return array Return array containing 'breakdown', bullet, 'target'.
   */
  public static function getStructTargetData($draft, $dr, $tsk)
  {
    $analysis = $dr->getAnalysis();
    $text = $dr->getParasenttok();

    $breakdown = array();
    foreach ($text as $index => &$par)
    {
      foreach ($par as $index2 => $sent)
      {
        $tt = $sent['text'];
        $tag = $sent['tag'];
        $count = str_word_count($tt, 0);
        if (!isset($breakdown[$tag])) {
          $breakdown[$tag] = 0;
        }
        $breakdown[$tag] += $count;
      }
    }


    $wc = $analysis->se_stats->number_of_words;
    $tg = $tsk->wordcount;
    $target= array(
        'target' => $tg,
        'total' => $wc,
        'range' => array("low" => intval($tg * 0.9), "high" => intval($tg * 1.1)),
        'inlimit' => ($wc <= ($tg * 1.1) && $wc >= ($tg * 0.9))
    );

    $distribution = array();
    $bullet = array();


    foreach ($breakdown as $id => $count)
    {
      $std = self::GetStructureData($id);
      // Was: $std = UserController::GetStructureData($id);
      $tt = array(
          'tag' => $id,
          'name' => $std['name'],
          'color' => $std['idx'],
          'y' => $count);

      if (in_array($id, array('#+s:c#','#+s:i#')))
      {
        $tt['sliced'] = true;
        $tt['selected'] = true;
      }

      $distribution[] = $tt;
      $bullet[] = array(
          'tag' => $id,
          'name' => $std['name'],
          'color' => $std['idx'],
          'data'=> array($count));

    }

    usort($distribution, function ($a, $b)
    {
      return ($b[ 'color' ]) - ($a[ 'color' ]);
    });
    usort($bullet, function ($a, $b)
    {
      return ($a[ 'color' ]) - ($b[ 'color' ]);
    });

    return array(
        'breakdown' => 	$distribution,
        'bullet' => 	$bullet,
        'target' =>		$target
    );
  }
}
