<?php

namespace CMS;

class Browser {

	private function __construct() {}
	private function __clone() {}

	/**
	 * Get the current browsing client's user agent string
	 *
	 * @return string|bool          The User Agent string if set, or false otherwise
	 */
	public static function getUserAgentString() {
		return isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : false;
	}

	/**
	 * Get the current browsing client's IP address
	 *
	 * @return string|bool          The IP address if set, or false otherwise
	 */
	public static function getIP() {
		$ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');
		foreach ($ip_keys as $key) {
			if (array_key_exists($key, $_SERVER) === true) {
				foreach (explode(',', $_SERVER[$key]) as $ip) {
					// trim for safety measures
					$ip = trim($ip);
					// attempt to validate IP
					if (Library\Validate::IP($ip)) {
						return $ip;
					}
				}
			}
		}

		return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false;
	}

	/**
	 * Scrub an IP address, setting the last value to 0 (used in session authentication)
	 *
	 * @param string $ip            The IP address to scrub
	 *
	 * @return string               The scrubbed IP address
	 */
	public static function trimIP($ip) {
		$pos = strrpos($ip, '.');
		if ($pos !== false) {
			$ip = substr($ip, 0, $pos+1);
		}
		return $ip . '0';
	}

	/**
	 * Redirect the user to a new location
	 *
	 * @param string $location      The page to redirect the user to. Relative or absolute links are accepted.
	 */
	public static function redirect($location) {
		header("Location: " . $location);
//		exit();
	}

}