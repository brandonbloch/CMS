<?php

class Theme extends SettingAbstract {

	// Make these private to prevent instantiation
	private function __construct() {}
	private function __clone() {}

	// these theme settings are read from a single file, which we do only once
	private static $themeFileRead = false;
	private static $themeSettings = array();

	// each of these options is retrieved from the database on first request
	private static $activeTheme;
	private static $colorScheme;

	private static $colorSchemes = array(
		0 => "Dark",
		1 => "Light",
	);

	public static function getColorSchemeList() {
		return self::$colorSchemes;
	}

	public static function getColorScheme() {
		if (self::$colorScheme === NULL) {
			self::$colorScheme = self::getValueFromDatabase("color_scheme");
		}
		return self::$colorScheme;
	}

	public static function setColorScheme($code) {
		if (!Validate::int($code)) {
			throw new InvalidArgumentException("Theme::setColorScheme expected int, got " . gettype($code) . " instead.");
		}
		if (!array_key_exists($code, self::$colorSchemes)) {
			throw new InvalidArgumentException("Nonexistent code supplied to Theme::setColorScheme.");
		}
		self::$colorScheme = (int) $code;
		self::saveValueToDatabase($code, "color_scheme");
	}

	/**
	 * @return string
	 */
	public static function getActiveTheme() {
		if (self::$activeTheme === NULL) {
			self::$activeTheme = self::getValueFromDatabase("active_theme");
		}
		return self::$activeTheme;
	}

	/**
	 * @return string
	 */
	public static function getThemeDirectory() {
		return "themes/" . self::getActiveTheme();
	}

	/**
	 * @return string
	 */
	public static function getThemeDirectoryURL() {
		return Site::getBaseURL() . "/" . self::getThemeDirectory();
	}

	/**
	 * @param string $theme
	 */
	public static function setActiveTheme($theme) {
		if (!is_string($theme)) {
			throw new InvalidArgumentException("Theme::setActiveTheme expected string, got " . gettype($theme) . " instead.");
		}
		if (!Validate::plainText($theme)) {
			throw new InvalidArgumentException("Invalid string content supplied to Theme::setActiveTheme.");
		}
		self::$activeTheme = $theme;
		self::saveValueToDatabase($theme, "active_theme");
	}

	/**
	 * @return string
	 */
	public static function getStylesheetLink() {
		return self::getThemeDirectoryURL() . "/style.css";
	}

	/**
	 *
	 */
	public static function includeHeader() {
		if (!file_exists(self::getThemeDirectory() . "/header.php")) {
			throw new RuntimeException("Theme '" . self::getActiveTheme() . "' does not have a header.php file.");
		}
		include self::getThemeDirectory() . "/header.php";
	}

	/**
	 *
	 */
	public static function includeFooter() {
		if (!file_exists(self::getThemeDirectory() . "/footer.php")) {
			throw new RuntimeException("Theme '" . self::getActiveTheme() . "' does not have a footer.php file.");
		}
		include self::getThemeDirectory() . "/footer.php";
	}

	/**
	 * @return string
	 */
	public static function getHTMLClasses() {
		// TODO implement this function
		return "logged-in";
	}

	/**
	 * @return string
	 */
	public static function getBodyClasses() {
		// TODO implement this function
		return "logged-in";
	}


	/**
	 * @param string $themeName
	 *
	 * @return array
	 */
	public static function readThemeFile($themeName) {
		$file = "themes/" . $themeName . "/theme.txt";
		if (!file_exists($file)) {
			throw new RuntimeException("Theme file missing from theme '" . $themeName . "' folder.");
		}
		return json_decode(Format::convertSmartQuotes(file_get_contents($file)), true);
	}

	/**
	 *
	 */
	private static function loadActiveThemeFile() {
		self::$themeSettings = self::readThemeFile(self::getActiveTheme());
	}

	/**
	 * @return string
	 */
	public static function getThemeName() {
		if (!self::$themeFileRead) {
			self::loadActiveThemeFile();
		}
		if (isset(self::$themeSettings["name"])) {
			return self::$themeSettings["name"];
		}
		return "";
	}

	/**
	 * @return string
	 */
	public static function getThemeDescription() {
		if (!self::$themeFileRead) {
			self::loadActiveThemeFile();
		}
		if (isset(self::$themeSettings["description"])) {
			return self::$themeSettings["description"];
		}
		return "";
	}

	/**
	 * @return string
	 */
	public static function getThemeAuthor() {
		if (!self::$themeFileRead) {
			self::loadActiveThemeFile();
		}
		if (isset(self::$themeSettings["author"])) {
			return self::$themeSettings["author"];
		}
		return "";
	}

	/**
	 * @return string
	 */
	public static function getThemeVersion() {
		if (!self::$themeFileRead) {
			self::loadActiveThemeFile();
		}
		if (isset(self::$themeSettings["version"])) {
			return self::$themeSettings["version"];
		}
		return "";
	}

	public static function getNavigationArray() {
		$pages = array();
		foreach (Page::getTopLevelWithoutContent() as $topLevelPage) {
			$array = array(
				"page" => $topLevelPage,
				"children" => self::getNavigationArrayRecursive($topLevelPage),
			);
			$pages[] = $array;
		}
		return $pages;
	}

	private static function getNavigationArrayRecursive(Page $parent) {
		$subPages = array();
		foreach ($parent->getChildren() as $child) {
			$array = array(
				"page" => $child,
				"children" => self::getNavigationArrayRecursive($child),
			);
			$subPages[] = $array;
		}
		return $subPages;
	}

	public static function getTopLevelNavigationMenu($useNav = false) {
		return self::getNavigationMenuRecursive($useNav, Page::getTopLevelWithoutContent(), true);
	}

	public static function getNavigationMenu($useNav = false) {
		return self::getNavigationMenuRecursive($useNav, Page::getTopLevelWithoutContent());
	}
	
	private static function getNavigationMenuRecursive($useNav, array $pageSet, $topLevelOnly = false) {

		$string = "";

		// wrap in a <nav> if desired
		if ($useNav) {
			$string .= "<nav>";
		}

		// opening tag of the structure
		$string .= '<ul class="navigation">' . PHP_EOL;

		// build the elements (recursively)
		foreach ($pageSet as $page) {
			// include the list item tag, or skip ir for navs
			$string .= '<li>';

			// include the page
			$string .= '<a href="' . $page->getURL() . '">' . $page->getShortname() . '</a>';

			// include any sub-pages recursively, if they were desired
			if (!$topLevelOnly) {
				$children = $page->getChildren();
				if (count($children) > 0) {
					$string .= PHP_EOL;
					$string .= self::getNavigationMenuRecursive(false, $children, false);
				}
			}

			// include the closing list item tag if necessary
			$string .= '</li>' . PHP_EOL;
		}

		// closing tag of the structure
		$string .= '</ul>' . PHP_EOL;

		// include the closing </nav> if necessary
		if ($useNav) {
			$string .= "</nav>";
		}

		return $string;
	}

}