<?php
/**
 * Group model.
 *
 * @package   OpenEssayist-slim
 * @copyright © 2013-2018 The Open University. (Institute of Educational Technology)
 */

class Group extends Model {
 	/**
 	 *
 	 * @key		Users/group_id
 	 * @return 	ORMWrapper
 	 */
 	public function users() {
 		return $this->has_many('Users');
 	}

 	/**
 	 * @key		Task/group_id
 	 * @return ORMWrapper
 	 */
 	public function tasks() {
 		return $this->has_many('Task');
 	}

}
