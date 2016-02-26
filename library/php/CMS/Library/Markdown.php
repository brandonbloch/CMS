<?php

namespace CMS\Library;

class Markdown {

	private static $parsedownInstance;

	private function __construct() {}
	private function __clone() {}

	public static function parse($markdown) {
		if (self::$parsedownInstance === NULL) {
			self::$parsedownInstance = new Parsedown();
		}
		return self::$parsedownInstance->text($markdown);
	}

}