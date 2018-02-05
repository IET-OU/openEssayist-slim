<?php
/**
 * Draft model.
 *
 * @package   OpenEssayist-slim
 * @copyright © 2013-2018 The Open University. (Institute of Educational Technology)
 */

/**
 *
 * @author Nicolas Van Labeke (https://github.com/vanch3d)
 *
 */
class Draft extends Model {

	/**
	 *
	 * @key		Draft/task_id
	 * @return 	Task
	 * @see 	ORMWrapper
	 */
	public function task() {
		return $this->belongs_to('Task');
	}

	/**
	 * @key		Draft/user_id
	 * @return 	Users
	 * @see 	ORMWrapper
	 */
	public function user() {
		return $this->belongs_to('Users');
	}

	/**
	 *
	 * @key		KWCategory/draft_id
	 * @return 	ORMWrapper
	 */
	public function kwCategories() {
		return $this->has_one('KWCategory');
	}

	/**
	 *
	 * @return array
	 */
	public function getParasenttok()
	{
		$data = array();
		$json = $this->analysis;
		$rr = json_decode($json, true);
		return $rr['se_data']['se_parasenttok'];
	}

	public function getAnalysis($assoc = false)
	{
		$json = $this->analysis;
		$rr = json_decode($json, $assoc);
		return $rr;
	}
}

// Moved: class KWCategory extends Model.
