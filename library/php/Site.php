<?php

class Site extends SettingAbstract {

	// Make these private to prevent instantiation
	private function __construct() {}
	private function __clone() {}

	// the various option types settings can take on
	const SETTING_TYPE_SHORT_TEXT = 1;
	const SETTING_TYPE_TOGGLE = 2;
	const SETTING_TYPE_LONG_TEXT = 3;

	private static $title;
	private static $description;
	private static $adminName;
	private static $adminEmail;

	private static $baseURL;

	public static function getBaseURL() {
		if (self::$baseURL === NULL) {
			// set the base URL on first call
			if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] != "" && $_SERVER["HTTPS"] != "off") {
				self::$baseURL .= "https://";
			} else {
				self::$baseURL .= "http://";
			}
			self::$baseURL .= $_SERVER["HTTP_HOST"];
			self::$baseURL .= dirname($_SERVER["SCRIPT_NAME"]);
		}
		return self::$baseURL;
	}

	/**
	 * @return string
	 */
	public static function getTitle() {
		if (self::$title === NULL) {
			self::$title = self::getValueFromDatabase("site_title");
		}
		return self::$title;
	}

	/**
	 * @param string $title
	 */
	public static function setTitle($title) {
		if (!is_string($title)) {
			throw new InvalidArgumentException("Site::setTitle expected string, got " . gettype($title) . " instead.");
		}
		if (!Validate::plainText($title)) {
			throw new InvalidArgumentException("Invalid string content supplied to Site::setTitle.");
		}
		self::$title = $title;
		self::saveValueToDatabase($title, "site_title");
	}

	/**
	 * @return string
	 */
	public static function getDescription() {
		if (self::$description === NULL) {
			self::$description = self::getValueFromDatabase("site_description");
		}
		return self::$description;
	}

	/**
	 * @param string $description
	 */
	public static function setDescription($description) {
		if (!is_string($description)) {
			throw new InvalidArgumentException("Site::setDescription expected string, got " . gettype($description) . " instead.");
		}
		if (!Validate::plainText($description)) {
			throw new InvalidArgumentException("Invalid string content supplied to Site::setDescription.");
		}
		self::$description = $description;
		self::saveValueToDatabase($description, "site_description");
	}

	public static function getAdminName() {
		if (self::$adminName === NULL) {
			self::$adminName = self::getValueFromDatabase("admin_name");
		}
		return self::$adminName;
	}

	public static function setAdminName($name) {
		if (!is_string($name)) {
			throw new InvalidArgumentException("Site::setAdminName expected string, got " . gettype($name) . " instead.");
		}
		if (!Validate::name($name)) {
			throw new InvalidArgumentException("Invalid name supplied to Site::setAdminName.");
		}
		self::$adminName = $name;
		self::saveValueToDatabase($name, "admin_name");
	}

	public static function getAdminEmail() {
		if (self::$adminEmail === NULL) {
			self::$adminEmail = self::getValueFromDatabase("admin_email");
		}
		return self::$adminEmail;
	}

	public static function setAdminEmail($email) {
		if (!is_string($email)) {
			throw new InvalidArgumentException("Site::setAdminEmail expected string, got " . gettype($email) . " instead.");
		}
		if (!Validate::email($email)) {
			throw new InvalidArgumentException("Invalid email address supplied to Site::setAdminEmail.");
		}
		self::$adminEmail = $email;
		self::saveValueToDatabase($email, "admin_email");
	}

	public static function set404Response() {
		http_response_code(404);
		if (file_exists(Theme::getThemeDirectory() . "/404.php")) {
			include Theme::getThemeDirectory() . "/404.php";
			die();
		}
		include "404.php";
		die();
	}

}