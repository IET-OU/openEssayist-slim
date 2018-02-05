<?php
/**
 * Default model for users.
 *
 * @package   OpenEssayist-slim
 * @copyright © 2013-2018 The Open University. (Institute of Educational Technology)
 */

/**
 *
 * @author Nicolas Van Labeke (https://github.com/vanch3d)
 *
 */
class Users extends Model {
	/**
	 * @key		Users/group_id
	 * @return	ORMWrapper
	 */
	public function group() {
		return $this->belongs_to('Group');
	}

	/**
	 * @key		Draft/users_id
	 * @return 	ORMWrapper
	 */
	public function drafts() {
		return $this->has_many('Draft');
	}

	/**
	 *
	 * @key		Note/users_id
	 * @return 	ORMWrapper
	 */
	public function Notes() {
		return $this->has_one('Note');
	}

	/**
	 *
	 * @key		Feedback/users_id
	 * @return 	ORMWrapper
	 */
	public function reports() {
		return $this->has_many('Feedback');
	}

}

// Moved: 'class Group extends Model'.

// Moved: 'class Task extends Model'.

// Moved: 'class Note extends Model'.

// Moved: 'class Feedback extends Model'.

// End.
