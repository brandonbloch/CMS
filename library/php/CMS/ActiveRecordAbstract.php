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

	protected abstract function insert(): bool;

	protected abstract function update(): bool;

	public function save(): bool {
		if (!isset($this->id) || is_null($this->id) || $this->id == 0) {
			return $this->insert();
		}
		return $this->update();
	}

	abstract function delete(): bool;

	public function getID(): int {
		return $this->id;
	}

	protected function setID(int $id) {
		$this->id  = $id;
	}

}