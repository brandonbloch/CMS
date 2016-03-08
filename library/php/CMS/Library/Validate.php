<?php

namespace CMS\Library;

/**
 * Validate class
 *
 * A static class containing all the various validation filters used by WebServant
 *
 * @author Brandon Bloch
 * @version 1.0
 */
class Validate {

	const USERNAME_MIN_LENGTH = 8;
	const PASSWORD_MIN_LENGTH = 8;

	private static $initialized = false;

	private function __construct() {}
	private function __clone() {}

	/**
	 * Validate a string as plain text.
	 * Allows all characters except those used in HTML (<tag> </tag>) and PHP (<?php ?>)
	 *
	 * @param string $string            The string to validate
	 * @param bool $allowEmpty          Whether to allow an empty string as valid input
	 *
	 * @return bool                     True if the string is valid plain text, and false otherwise
	 */
	public static function plainText(string $string, bool $allowEmpty = false): bool {
		if (!isset($string) || is_null($string)) {
			return false;
		}
		if (trim($string) === "") {
			return ($allowEmpty) ? true : false;
		}
		return ($string === strip_tags($string));
	}

	/**
	 * Validate a string as HTML.
	 *
	 * @param string $string            The string to validate
	 * @param bool $allowEmpty          Whether to allow an empty string as valid input
	 *
	 * @return bool                     True if the string is valid HTML, and false otherwise
	 */
	public static function HTML(string $string, bool $allowEmpty = false): bool {
		if (!isset($string) || is_null($string)) {
			return false;
		}
		if (trim($string) === "") {
			return ($allowEmpty) ? true : false;
		}
		if (mb_stripos($string, "<?php") !== false || mb_stripos($string, "?>" !== false)) {
			return false;
		}
		return true;
	}

	/**
	 * Validate a string as a slug. 
	 * 
	 * @param string $slug          The slug to validate
	 *
	 * @return bool                 True if the string is a valid slug, and false otherwise
	 */
	public static function slug(string $slug): bool {
		if (!isset($slug) || is_null($slug)) {
			return false;
		}
		if (trim($slug) === "") {
			return false;
		}
		return preg_match("/[A-za-z0-9-]+/", $slug);
	}

	/**
	 * Check the validity of a given string as a name.
	 * Ensures the value is not empty, is plain text, and contains only letters
	 *
	 * @param string $name              The string to validate
	 *
	 * @return bool                     True if the string is a valid name, and false otherwise
	 */
	public static function name(string $name): bool {
		if (empty($name) || is_null($name)) {
			return false;
		}
		if (trim($name) === "") {
			return false;
		}
		if ($name !== strip_tags($name)) {
			return false;
		}
		// TODO allow more inclusive international names
		return (preg_match("/^[[:alpha:] -]+$/", $name));
	}

	/**
	 * Check the validity of a given string as an email address.
	 *
	 * @param string $email             The string to validate
	 *
	 * @return bool                     True if the string is a valid email address, and false otherwise
	 */
	public static function email(string $email): bool {
		if (empty($email) || is_null($email)) {
			return false;
		}
		if (trim($email) === "") {
			return false;
		}
		if ($email !== strip_tags($email)) {
			return false;
		}
		return filter_var($email, FILTER_VALIDATE_EMAIL) ? true : false;
	}

	/**
	 * Check the validity of a given string as a URL.
	 *
	 * @param string $url               The string to validate
	 *
	 * @return bool                     True if the string is a valid URL, and false otherwise
	 */
	public static function url(string $url): bool {
		if (empty($url) || is_null($url)) {
			return false;
		}
		if (trim($url) === "") {
			return false;
		}
		if ($url !== strip_tags($url)) {
			return false;
		}
		return filter_var($url, FILTER_VALIDATE_URL) ? true : false;
	}

	/**
	 * Check the validity of a given string as a username.
	 * The minimum length is specified in constants.php as USERNAME_MIN_LENGTH.
	 *
	 * @param string $username          The string to validate
	 *
	 * @return bool                     True if the string is a valid username, and false otherwise
	 */
	public static function username(string $username): bool {
		if (empty($username) || is_null($username)) {
			return false;
		}
		if (trim($username) === "") {
			return false;
		}
		if ($username !== strip_tags($username)) {
			return false;
		}
		if (mb_strlen($username) < self::USERNAME_MIN_LENGTH) {
			return false;
		}
		return (preg_match("/^[a-zA-Z0-9][a-zA-Z0-9_\\-\\.]*$/", $username) === 1);
	}

	/**
	 * Check the validity of a given string as a password
	 *
	 * @param string $password          The string to validate
	 *
	 * @return bool                     True if the string is a valid password, and false otherwise
	 */
	public static function password(string $password): bool {
		if (empty($password) || is_null($password)) {
			return false;
		}
		if (trim($password) === "") {
			return false;
		}
		if ($password !== strip_tags($password)) {
			return false;
		}
		if (mb_strlen($password) < self::PASSWORD_MIN_LENGTH) {
			return false;
		}
		return true;
	}

	/**
	 * Ensures an IP address is a valid IP, and also does not fall within a private network range.
	 *
	 * @param string $ip        The IP address to check
	 *
	 * @return bool             True if the IP is valid and not private
	 */
	public static function IP(string $ip): bool {
		if (filter_var($ip, \FILTER_VALIDATE_IP, \FILTER_FLAG_IPV4 | \FILTER_FLAG_NO_PRIV_RANGE | \FILTER_FLAG_NO_RES_RANGE) === false) {
			return false;
		}
		return true;
	}

}