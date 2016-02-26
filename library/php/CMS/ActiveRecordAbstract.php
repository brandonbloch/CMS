<?php

namespace CMS;

/**
 * Class ActiveRecordAbstract
 *
 * A class to be extended when implementing the active record pattern.
 *
 * The child class will be stored in a database indexed by a primary ID.
 * It must implement the insert(), update(), and delete() methods.
 *
 * insert() and update() are never called directly; save() is called in all cases and will in turn
 * call insert() or update() depending on whether the record's ID has been set yet. Thus, insert()
 * should finish by setting the instance's ID to the record's newly-created value in the database.
 */
abstract class ActiveRecordAbstract {

	protected $id;

	protected abstract function insert();

	protected abstract function update();

	public function save() {
		if (!isset($this->id) || $this->id == 0) {
			return $this->insert();
		}
		return $this->update();
	}

	abstract function delete();

	public function getID() {
		return $this->id;
	}

	protected function setID($id) {
		if (is_int($id)) {
			$this->id = $id;
		} else {
			try {
				$id = (int) $id;
				$this->id  = $id;
			} catch (\Exception $e) {
				throw new \InvalidArgumentException("Expected int for instance ID, got " . gettype($id) . " instead.");
			}
		}
	}

}