<?php
/**
 * Note model.
 *
 * @package   OpenEssayist-slim
 * @copyright © 2013-2018 The Open University. (Institute of Educational Technology)
 */

class Note extends Model
{
	/**
	 *
	 * @key		Note/users_id
	 * @return 	User
	 * @see 	ORMWrapper
	 */
	public function user() {
		return $this->belongs_to('Users');
	}

}
