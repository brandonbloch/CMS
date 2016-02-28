<?php

namespace CMS;

/**
 * Class Auth
 *
 * A class providing a suite of authorization and authentication functions.
 *
 * Uses sessions for authorization.
 * Also handles password hashing and generation of password-reset keys.
 */
class Auth {

	/**
	 * The algorithmic cost value used by the hash() function
	 */
	const COST_VALUE = 12;
	
	private static $initialized = false;

	private function __construct() {}
	private function __clone() {}

	/**
	 * Start the session
	 *
	 * Used to ensure the proper session headers are sent before any output occurs.
	 */
	public static function initialize() {
		if (self::$initialized) {
			return; // only needs to run once per page request
		}
		if (session_status() == PHP_SESSION_NONE) { // start the session if it isn't yet
			session_start();
		}
		self::$initialized = true;
	}

	// initialize session variables
	private static function sessionStart() {
		self::initialize();
		self::sessionRegenerate();
		if (!isset($_SESSION['active'])) {
			$_SESSION['active'] = 1;
			$_SESSION['ip_address'] = Browser::trimIP(self::get_ip_address());
			$_SESSION['user_agent'] = (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : '';
			$_SESSION['logged_in'] = 0;
			$_SESSION['last_activity'] = time();
			$_SESSION['last_login'] = 0;
			$_SESSION['idle'] = 0;
		}
	}
	
	// regenerate the session ID and delete the old associated session file
	private static function sessionRegenerate() {
		self::initialize();
		session_regenerate_id(true);
	}
	
	// destroy session variables
	private static function sessionEnd() {
		self::initialize();
		session_unset();	
		session_destroy();
	}
	
	// log them out, and set the idle flag
	private static function sessionHalt() {
		self::initialize();
		$_SESSION['logged_in'] = 0;
		$_SESSION['idle'] = 1;
	}
	
	// log them in, disable the idle flag, and update timestamps
	private static function sessionResume() {
		self::initialize();
//		self::sessionRegenerate();
		$_SESSION['logged_in'] = 1;
		$_SESSION['idle'] = 0;
		$_SESSION['last_activity'] = time();
		$_SESSION['last_login'] = time();
	}

	/**
	 * Authenticate the current session
	 *
	 * @return bool     True if the session has been authenticated, and false if not
	 */
	public static function authenticate() {
		self::initialize();
		self::sessionStart(); // start the session if necessary

		if ($_SESSION['logged_in'] == 1) { // if logged in, ensure session validity
			
			// if the maximum idle time is exceeded, halt the session
			if (\SESSION_IDLE_TIME >= 0 && time() > $_SESSION['last_activity'] + \SESSION_IDLE_TIME) {
				self::sessionHalt();
				return false;
			} else {
				// update the time of activity to now
				$_SESSION['last_activity'] = time();
				// if the IP address has changed, destroy the session
				if ($_SESSION['ip_address'] !== self::trimIP(self::get_ip_address()) ) {
					self::sessionEnd();
					return false;
				} else {
					// if the user agent has changed/is invalid, destroy the session
					if (!isset($_SERVER['HTTP_USER_AGENT']) || $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
						self::sessionEnd();
						return false;
					} else {
						// any additional checks added in the future should be placed here
					}
				}
			}
			return true;
		}
		
		return false;
	}

	/**
	 * Attempt to log in and create a session with the specified username and password
	 *
	 * @param string $username      The username to log in with
	 * @param string $password      The password to log in with
	 *
	 * @return bool                 True if the login attempt is successful, and false if not
	 */
	public static function login($username, $password) {
		self::initialize();
		if (!Validate::username($username)) {
			throw new \InvalidArgumentException("Invalid username supplied as argument.");
		}
		if (!Validate::password($password)) {
			throw new \InvalidArgumentException("Invalid password supplied as argument.");
		}

		try {
			$pdo  = DB::getHandle();
			$stmt = $pdo->prepare("SELECT pid, pass FROM users WHERE username = :username");
			$stmt->bindParam(":username", $username);
			$stmt->execute();
			$user = $stmt->fetch();
			if ($user === false) {
				throw new \PDOException();
			}
		} catch (\PDOException $e) {
			return false;
		}

		$_SESSION['user_id'] = $user['pid'];

		if (self::verify($password, $user['pass'])) {
			self::sessionResume();
			if (self::authenticate()) {
				User::current()->updateLoginTimestamp();
				return true;
			}
		}
		return false;
	
	}

	/**
	 * Check if the current user's session is idle.
	 *
	 * @return bool         True if the session is idle, false if not
	 */
	public static function isIdle() {
		self::initialize();
		if (\SESSION_IDLE_TIME <= 0) {
			return false;
		}
		if (isset($_SESSION['idle']) && $_SESSION['idle'] == 1) {
			return true;
		}
		return false;
	}

	/**
	 * Get a text message to display regarding the user's idle status
	 *
	 * @return string       The idle message
	 */
	public static function idleMessage() {
		if (Auth::isIdle()) {
			if (\SESSION_IDLE_TIME == 1) {
				return "You have been logged out due to 1 second of inactivity.";
			}
			if (\SESSION_IDLE_TIME < 60) {
				return "You have been logged out due to " . \SESSION_IDLE_TIME . " seconds of inactivity.";
			}
			if (\SESSION_IDLE_TIME < 120) {
				return "You have been logged out due to 1 minute of inactivity.";
			}
			if (\SESSION_IDLE_TIME < 3600) {
				return "You have been logged out due to " . floor(\SESSION_IDLE_TIME / 60) . " minutes of inactivity.";
			}
			if (\SESSION_IDLE_TIME < 7200) {
				return "You have been logged out due to 1 hour of inactivity.";
			}
			return "You have been logged out due to " . floor(\SESSION_IDLE_TIME / 3600) . " hours of inactivity.";
		} else {
			return "";
		}
	}

	/**
	 * Check if the current user is logged in
	 *
	 * @return bool         True if the session exists and the user is logged in, and false if not
	 */
	public static function isLoggedIn() {
		if (session_status() == \PHP_SESSION_NONE) {
			return false;
		}
		self::initialize();
		if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == 1) {
			return true;
		}
		return false;
	}

	/**
	 * Log the current user out, if they are logged in
	 */
	public static function logout() {
		self::initialize();
		self::sessionEnd();
		header('Location: ' . Site::getBaseURL());
		exit();
	}

	/**
	 * Get a pseudo-random reset key, to be stored with a user record for use in password resetting
	 *
	 * @return string           A pseudo-random alphanumeric key
	 */
	public static function getNewResetKey() {
		self::initialize();
		$characters = "abcdefghijklmnopqrstuvwxyz0123456789";
		$numChars = strlen($characters);
		$key = "";
		for ($i = 0; $i < 23; $i++) {
			$key .= $characters[mt_rand(0, $numChars - 1)];
		}
		return $key;
	}

	/**
	 * Hash a password using a provided salt
	 *
	 * @param string $password      The password to be hashed
	 *
	 * @return string               The hashed password
	 *
	 */
	public static function hash($password) {
		self::initialize();
		$options = array("cost" => self::COST_VALUE);
		return password_hash($password, \PASSWORD_DEFAULT, $options);
	}

	/**
	 * Check a non-hashed password against an already-hashed password
	 *
	 * @param string $password      The password attempt to be verified
	 * @param string $hash          The hashed password to string to check against
	 *
	 * @return bool                 True if the passwords match, and false otherwise
	 */
	public static function verify($password, $hash) {
		self::initialize();
		return password_verify($password, $hash);
	}

	/**
	 * Check two passwords (both hashed or non-hashed) for equality. Used in password confirmations.
	 *
	 * @param string $password1     The first password
	 * @param string $password2     The second password
	 *
	 * @return bool                 True if the passwords match, and false otherwise
	 */
	public static function passMatch($password1, $password2) {
		self::initialize();
		if (Library\Validate::password($password1) === false) {
			return false;
		}
		if (Library\Validate::password($password2) === false) {
			return false;
		}
		return ($password1 === $password2);
	}
	
}
