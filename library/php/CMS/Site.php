<?php

namespace CMS;

class Site extends SettingAbstract {

	const SETTING_TYPE_SHORT_TEXT   = 1;
	const SETTING_TYPE_TOGGLE       = 2;
	const SETTING_TYPE_LONG_TEXT    = 3;

	private function __construct() {}
	private function __clone() {}

	private static $title;
	private static $description;
	private static $adminName;
	private static $adminEmail;

	private static $baseURL;

	public static function getBaseURL(): string {
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
	public static function getTitle(): string {
		if (self::$title === NULL) {
			self::$title = self::getValueFromDatabase("site_title");
		}
		return self::$title;
	}

	/**
	 * @param string $title
	 */
	public static function setTitle(string $title) {
		if (!Library\Validate::plainText($title)) {
			throw new \InvalidArgumentException("Invalid string content supplied to Site::setTitle.");
		}
		self::$title = $title;
		self::saveValueToDatabase($title, "site_title");
	}

	/**
	 * @return string
	 */
	public static function getDescription(): string {
		if (self::$description === NULL) {
			self::$description = self::getValueFromDatabase("site_description");
		}
		return self::$description;
	}

	/**
	 * @param string $description
	 */
	public static function setDescription(string $description) {
		if (!Library\Validate::plainText($description)) {
			throw new \InvalidArgumentException("Invalid string content supplied to Site::setDescription.");
		}
		self::$description = $description;
		self::saveValueToDatabase($description, "site_description");
	}

	/**
	 * @return string
	 */
	public static function getAdminName(): string {
		if (self::$adminName === NULL) {
			self::$adminName = self::getValueFromDatabase("admin_name");
		}
		return self::$adminName;
	}

	/**
	 * @param string $name
	 */
	public static function setAdminName(string $name) {
		if (!Library\Validate::plainText($name)) {
			throw new \InvalidArgumentException("Invalid name supplied to Site::setAdminName.");
		}
		self::$adminName = $name;
		self::saveValueToDatabase($name, "admin_name");
	}

	/**
	 * @return string
	 */
	public static function getAdminEmail(): string {
		if (self::$adminEmail === NULL) {
			self::$adminEmail = self::getValueFromDatabase("admin_email");
		}
		return self::$adminEmail;
	}

	/**
	 * @param string $email
	 */
	public static function setAdminEmail(string $email) {
		if (!Library\Validate::email($email)) {
			throw new \InvalidArgumentException("Invalid email address supplied to Site::setAdminEmail.");
		}
		self::$adminEmail = $email;
		self::saveValueToDatabase($email, "admin_email");
	}

	/**
	 *
	 */
	public static function set404Response() {
		http_response_code(404);
		if (file_exists(Theme::getThemeDirectory() . "/404.php")) {
			include Theme::getThemeDirectory() . "/404.php";
			die();
		}
		include "404.php";
		die();
	}

	/**
	 *
	 */
	public static function set403Response() {
		http_response_code(403);
		if (file_exists(Theme::getThemeDirectory() . "/403.php")) {
			include Theme::getThemeDirectory() . "/403.php";
			die();
		}
		include "403.php";
		die();
	}

}