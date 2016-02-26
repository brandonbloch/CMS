<?php

namespace CMS;

abstract class SettingAbstract {

	protected static function getValueFromDatabase($shortname) {
		try {
			$pdo    = DB::getHandle();
			$stmt   = $pdo->prepare("SELECT value FROM settings WHERE short_name = :short_name");
			$stmt->bindParam(":short_name", $shortname);
			$stmt->execute();
			$result = $stmt->fetch();
			if ($result === false) {
				throw new \PDOException();
			}
		} catch (\PDOException $e) {
			throw new \RuntimeException("Unable to retrieve " . $shortname . " from the database.");
		}
		return $result["value"];
	}

	protected static function saveValueToDatabase($value, $shortname) {
		try {
			$pdo   = DB::getHandle();
			$stmt = $pdo->prepare("UPDATE settings SET value = :value WHERE short_name = :short_name");
			$stmt->bindParam(":value", $value);
			$stmt->bindParam(":short_name", $shortname);
			$stmt->execute();
		} catch (\PDOException $e) {
			throw new \RuntimeException("Unable to save " . $shortname . " to the database.");
		}
	}

}