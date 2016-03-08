<?php

namespace CMS;

class Theme extends SettingAbstract {

	// Make these private to prevent instantiation
	private function __construct() {}
	private function __clone() {}

	// these theme settings are read from a single file, which we do only once
	private static $themeFileRead = false;
	private static $themeSettings = [];

	// each of these options is retrieved from the database on first request
	private static $activeTheme;
	private static $colorScheme;

	private static $colorSchemes = [
		0 => "Dark",
		1 => "Light"
	];

	public static function getColorSchemeList(): array {
		return self::$colorSchemes;
	}

	public static function getColorScheme(): int {
		if (self::$colorScheme === NULL) {
			self::$colorScheme = self::getValueFromDatabase("color_scheme");
		}
		return self::$colorScheme;
	}

	public static function setColorScheme(int $code) {
		if (!array_key_exists($code, self::$colorSchemes)) {
			throw new \InvalidArgumentException("Nonexistent code supplied to Theme::setColorScheme.");
		}
		self::$colorScheme = $code;
		self::saveValueToDatabase($code, "color_scheme");
	}

	/**
	 * @return string
	 */
	public static function getActiveTheme(): string {
		if (self::$activeTheme === NULL) {
			self::$activeTheme = self::getValueFromDatabase("active_theme");
		}
		return self::$activeTheme;
	}

	/**
	 * @return string
	 */
	public static function getThemeDirectory(): string {
		return "themes/" . self::getActiveTheme();
	}

	/**
	 * @return string
	 */
	public static function getThemeDirectoryURL(): string {
		return Site::getBaseURL() . "/" . self::getThemeDirectory();
	}

	/**
	 * @param string $theme
	 */
	public static function setActiveTheme(string $theme) {
		if (!Library\Validate::plainText($theme)) {
			throw new \InvalidArgumentException("Invalid string content supplied to Theme::setActiveTheme.");
		}
		self::$activeTheme = $theme;
		self::saveValueToDatabase($theme, "active_theme");
	}

	/**
	 * @return string
	 */
	public static function getStylesheetLink(): string {
		return self::getThemeDirectoryURL() . "/style.css";
	}

	/**
	 *
	 */
	public static function includeHeader() {
		if (!file_exists(self::getThemeDirectory() . "/header.php")) {
			throw new \RuntimeException("Theme '" . self::getActiveTheme() . "' does not have a header.php file.");
		}
		include self::getThemeDirectory() . "/header.php";
	}

	/**
	 *
	 */
	public static function includeFooter() {
		if (!file_exists(self::getThemeDirectory() . "/footer.php")) {
			throw new \RuntimeException("Theme '" . self::getActiveTheme() . "' does not have a footer.php file.");
		}
		include self::getThemeDirectory() . "/footer.php";
	}

	/**
	 * @return string
	 */
	public static function getBodyClasses(): string {
		$classes = "";
		// TODO implement this
		$classes = Library\Format::addClass($classes, "logged-in");
		if (isset($_GET["edit"]) && !isset($_GET["settings"])) {
			$classes = Library\Format::addClass($classes, "editing");
		}
		return $classes;
	}

	/**
	 * @param string $themeName
	 *
	 * @return array
	 */
	public static function readThemeFile(string $themeName): array {
		$file = "themes/" . $themeName . "/theme.txt";
		if (!file_exists($file)) {
			throw new \RuntimeException("Theme file missing from theme '" . $themeName . "' folder.");
		}
		return json_decode(Library\Format::convertSmartQuotes(file_get_contents($file)), true);
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
	public static function getThemeName(): string {
		if (!self::$themeFileRead) {
			self::loadActiveThemeFile();
		}
		return self::$themeSettings["name"] ?? "";
	}

	/**
	 * @return string
	 */
	public static function getThemeDescription(): string {
		if (!self::$themeFileRead) {
			self::loadActiveThemeFile();
		}
		return self::$themeSettings["description"] ?? "";
	}

	/**
	 * @return string
	 */
	public static function getThemeAuthor(): string {
		if (!self::$themeFileRead) {
			self::loadActiveThemeFile();
		}
		return self::$themeSettings["author"] ?? "";
	}

	/**
	 * @return string
	 */
	public static function getThemeVersion() {
		if (!self::$themeFileRead) {
			self::loadActiveThemeFile();
		}
		return self::$themeSettings["version"] ?? "";
	}

	public static function getNavigationArray() {
		$pages = [];
		foreach (Page::getTopLevelWithoutContent() as $topLevelPage) {
			$pages[] = [
				"page" => $topLevelPage,
				"children" => self::getNavigationArrayRecursive($topLevelPage),
			];
		}
		return $pages;
	}

	private static function getNavigationArrayRecursive(Page $parent) {
		$subPages = [];
		foreach ($parent->getChildren() as $child) {
			$subPages[] = [
				"page" => $child,
				"children" => self::getNavigationArrayRecursive($child),
			];
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

			// only include pages (and their children) whose visibility is set to "public"
			if ($page->getVisibility() == Page::VISIBILITY_PUBLIC) {

				// include the list item tag, or skip ir for navs
				$string .= '<li>';

				// include the page
				$string .= '<a href="' . $page->getURL() . '">' . $page->getShortname() . '</a>';

				// include any sub-pages recursively, if they were desired
				if (!$topLevelOnly) {
					$children = $page->getPublicChildren();
					if (count($children) > 0) {
						$string .= PHP_EOL;
						$string .= self::getNavigationMenuRecursive(false, $children, false);
					}
				}

				// include the closing list item tag if necessary
				$string .= '</li>' . PHP_EOL;

			}
		}

		// closing tag of the structure
		$string .= '</ul>' . PHP_EOL;

		// include the closing </nav> if necessary
		if ($useNav) {
			$string .= "</nav>";
		}

		return $string;
	}

	public static function getTemplateForPageType($identifier) {
		try {
			$pageType = Pages::getPageType($identifier);
		} catch (\OutOfBoundsException $e) {
			$pageType = Pages::getPageType("default");
		}
		// if the set page type has a template file in the theme directory, use that first
		if (file_exists(self::getThemeDirectory() . "/" . $pageType["template"])) {
			return $pageType["template"];
		}
		// if no type template exists but the theme has a page.php file, use that
		if (file_exists(self::getThemeDirectory() . "/page.php")) {
			return "page.php";
		}
		// finally, fall back to the theme's index.php file
		return "index.php";
	}

}