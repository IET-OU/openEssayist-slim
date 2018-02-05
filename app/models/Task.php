<?php
/**
 * Task / assignment model.
 *
 * @package   OpenEssayist-slim
 * @copyright © 2013-2018 The Open University. (Institute of Educational Technology)
 */

/**
 * Default data model for the assignemnt (task)
 * @author Nicolas Van Labeke (https://github.com/vanch3d)
 *
 */
class Task extends Model {

	/**
	 * @key		Draft/task_id
	 * @return 	ORMWrapper
	 */
	public function drafts() {
		return $this->has_many('Draft');
	}

	/**
	 * @key		Task/group_id
	 * @return 	ORMWrapper
	 * @see 	Group
	 */
	public function group() {
		return $this->belongs_to('Group');
	}

	/**
	 * Return the short version (first sentence) of the assignment question
	 * @return string
	 */
	public function short()
	{
		$tt = preg_split('/(?<=[.?!;:])\s+/', $this->assignment);
		return "" . $tt[0];
	}

	/**
	 * Return the long version (all but first sentence) of the assignment question
	 * @return string
	 */
	public function long()
	{
		$tt = preg_split('/(?<=[.?!;:])\s+/', $this->assignment);
		array_shift($tt);
		return "".join(" ", $tt);
	}
}
