<?php
/**
 * KWCategory model.
 *
 * @package   OpenEssayist-slim
 * @copyright © 2013-2018 The Open University. (Institute of Educational Technology)
 */

class KWCategory extends Model {

	/**
	 *
	 * @key		KWCategory/draft_id
	 * @return 	Task
	 * @see 	ORMWrapper
	 */
	public function task() {
		return $this->belongs_to('Draft');
	}

	public function getGroups($assoc = true)
	{
		$json = $this->category;
		$rr = json_decode($json, $assoc);
		return $rr;
	}
}
