<?php

namespace CMS\Library;

class Markdown {

	private static $parsedownInstance;

	private function __construct() {}
	private function __clone() {}

	/**
	 * Parse Markdown input into the corresponding HTML output
	 *
	 * @param string $markdown      The input Markdown
	 *
	 * @return string               The output HTML
	 */
	public static function parse(string $markdown): string {
		if (self::$parsedownInstance === NULL) {
			self::$parsedownInstance = new Parsedown();
		}
		return self::$parsedownInstance->text($markdown);
	}

}