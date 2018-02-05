<?php
/**
 * Feedback model.
 *
 * @package   OpenEssayist-slim
 * @copyright © 2013-2018 The Open University. (Institute of Educational Technology)
 */

class Feedback extends Model
{
 	/**
 	 *
 	 * @key		Feedback/users_id
 	 * @return 	User
 	 * @see 	ORMWrapper
 	 */
 	public function user() {
 		return $this->belongs_to('Users');
 	}

}
