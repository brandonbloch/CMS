<?php

namespace CMS\Library;

/**
 * Format class
 *
 * A static class containing all the various format filters used by WebServant
 *
 * @author Brandon Bloch
 * @version 1.0
 */
class Format {

	const DATE_LONG         = "F j, Y";
	const DATE_SHORT        = "n/j/Y";
	const TIME              = "g:i A";
	const DATETIME_LONG     = "F j, y \\a\\t g:i A";
	const DATETIME_SHORT    = "n/j/Y g:i A";
	const MYSQL_DATE        = "Y-m-d";
	const MYSQL_TIMESTAMP   = "Y-m-d H:i:s";

	private function __construct() {}
	private function __clone() {}

	/**
	 * Append an ordinal suffix to an integer
	 *
	 * @param int $int          The integer to be displayed
	 *
	 * @return string           An ordinal suffix appended to the integer
	 */
	public static function ordinal(int $int): string {
		$abs = abs($int);
		$mod100 = $abs % 100;
		$ends   = ["th", "st", "nd", "rd", "th", "th", "th", "th", "th", "th"];
		if (($mod100) > 10 && ($mod100) < 14) {
			return $int . "th";
		}
		return $int . $ends[$abs % 10];
	}

	/**
	 * Truncate a string to a specified number of characters, appending an ellipsis if necessary
	 *
	 * @param string $string    The string to format
	 * @param int $maxChars     The maximum number of characters allowed before truncation
	 *
	 * @return string           The truncated string, with an ellipsis if any characters were cut off
	 */
	public static function truncate(string $string, int $maxChars = 50): string {
		return (mb_strlen($string) > $maxChars + 3) ? mb_substr($string, 0, $maxChars) . "..." : $string;
	}

	/**
	 * * Format a variety of date/time types as a relative date/time string.
	 * Examples include "3 hours ago", "Just now", "Last month", or "May 2013".
	 * Works for both past and future dates/times
	 *
	 * @param \DateTime $timestamp      The timestamp to be formatted
	 *
	 * @return string                   The relative time string
	 */
	public static function relativeTime(\DateTime $timestamp): string {
		// TODO this function isn't accurate enough (eg. "yesterday" isn't the same as "between 24 and 48 hours ago")
		$ts = $timestamp->getTimestamp();
		$diff = time() - $ts;
		if($diff == 0) {
        	return 'Now';
		} else if($diff > 0) {
			$day_diff = intdiv($diff, 86400);
			if($day_diff == 0) {
				if($diff < 60) return 'Just now';
				if($diff < 120) return '1 minute ago';
				if($diff < 3600) return intdiv($diff, 60) . ' minutes ago';
				if($diff < 7200) return '1 hour ago';
				if($diff < 86400) return intdiv($diff, 3600) . ' hours ago';
			}
			if($day_diff == 1) return 'Yesterday';
			if($day_diff < 7) return $day_diff . ' days ago';
			if($day_diff == 7) return '1 week ago';
			if($day_diff < 31) return ceil($day_diff / 7) . ' weeks ago';
			if($day_diff < 60) return 'Last month';
			return date('F Y', $ts);
		} else {
			$diff = abs($diff);
			$day_diff = intdiv($diff, 86400);
			if($day_diff == 0) {
				if($diff < 120) return 'In a minute';
				if($diff < 3600) return 'In ' . intdiv($diff, 60) . ' minutes';
				if($diff < 7200) return 'In an hour';
				if($diff < 86400) return 'In ' . intdiv($diff, 3600) . ' hours';
			}
			if($day_diff == 1) return 'Tomorrow';
			if($day_diff < 4) return date('l', $ts);
			if($day_diff < 7 + (7 - date('w'))) return 'Next week';
			if(ceil($day_diff / 7) < 4) return 'In ' . ceil($day_diff / 7) . ' weeks';
        	if(date('n', $ts) == date('n') + 1) return 'Next month';
        	return date('F Y', $ts);
    	}
	}

	/**
	 * Format a file size for display in appropriate units.
	 *
	 * @param int $size         The size to format
	 * @param int $precision    The number of decimal places to round to (defaults to 2)
	 *
	 * @return string           The formatted size
	 */
	public static function bytes(int $size, int $precision = 2): string {
		$prefixes = ["bytes", "kB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"];
		$magnitude = 0;
		while ($size >= 1024 || $size <= -1024) {
			$size = $size / 1024;
			$magnitude++;
		}
		if (!array_key_exists($magnitude, $prefixes)) {
			throw new \OutOfRangeException("The filesize is too large to format.");
		}
		return round($size, $precision) . " " . $prefixes[$magnitude];
	}

	/**
	 * Turn a string into a valid slug.
	 *
	 * @param string $string        The string to make the slug from
	 *
	 * @return string               The slug
	 */
	public static function slug(string $string): string {
		$slug = str_replace("-", " ", $string);
		$slug = mb_strtolower($slug);
		$slug = preg_replace("/[^A-Za-z0-9 ]/", "", $slug);
		$slug = preg_replace("/ +/", "-", $slug);
		return $slug;
	}

	/**
	 * Convert all curly "smart quotes" in a string to straight quotes.
	 * Useful when parsing data like a text file, where the OS may auto-insert these.
	 *
	 * @param string $string        The string to remove smart quotes from
	 *
	 * @return string               The string with smart quotes converted to straight quotes
	 */
	public static function convertSmartQuotes(string $string): string {
		// TODO include quotation marks from other languages?
		$search = ["“", "”", "‘", "’"];
		$replace = ["\"", "\"", "'", "'"];
		return str_replace($search, $replace, $string);
	}

	/**
	 * Add a class to a pre-existing string of classes, for output in HTML tags.
	 *
	 * @param string $classes       The current class string
	 * @param string $class         The class name to append
	 *
	 * @return string               The new class string with $class added
	 */
	public static function addClass(string $classes, string $class): string {
		if ($classes == "") {
			return $class;
		}
		if (!$class) {
			return $classes;
		}
		// TODO prevent duplicate re-adding of a class?
		// (would need to do a regex to match the whole name only, and not part of another class)
		return $classes . " " . $class;
	}

	/**
	 * Remove a class from a pre-existing string of classes, for output in HTML tags.
	 *
	 * @param string $classes       The current class string
	 * @param string $class         The class name to remove
	 *
	 * @return string               The new class string with $class removed
	 */
	public function removeClass(string $classes, string $class): string {
		if ($classes == "") {
			return $classes;
		}
		if (!$class) {
			return $classes;
		}
		$classes = str_replace($class, "", $classes);
		$classes = str_replace("  ", " ", $classes);
		return $classes;
	}
	
}
