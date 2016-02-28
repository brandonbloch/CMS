<?php

namespace CMS;

class Pages {

	private static $currentPage;
	private static $pageTypes = array(
		0 => array(
			"identifier" => "default",
			"name" => "Single Zone",
			"description" => "A blank page with one large editable zone.",
			"zones" => 1,
			"template" => "page.php",
			"icon" => "page-default.svg",
		)
	);

	/**
	 * @return Page
	 */
	public static function getCurrentPage() {
		return self::$currentPage;
	}

	/**
	 * @param Page $currentPage
	 */
	public static function setCurrentPage(Page $currentPage) {
		if (self::$currentPage !== NULL) {
			throw new \BadMethodCallException("The current page is already set.");
		}
		self::$currentPage = $currentPage;
	}

	public static function registerPageType($identifier, $name, array $options = array()) {
		$defaults = array(
			"icon" => NULL,
			"description" => NULL,
			"zones" => 1,
			"template" => "page.php",
		);
		$options = array_merge($defaults, $options);

		$type = array();
		if (!Library\Validate::plainText($identifier)) {
			throw new \InvalidArgumentException("Invalid page type identifier supplied to Pages::registerPageType.");
		}
		foreach (self::$pageTypes as $existingType) {
			if ($existingType["identifier"] == $identifier) {
				throw new \InvalidArgumentException("Page type identifier '" . $identifier . "' already exists.");
			}
		}
		$type["identifier"] = $identifier;
		if (!Library\Validate::plainText($name)) {
			throw new \InvalidArgumentException("Invalid page type name supplied to Pages::registerPageType.");
		}
		$type["name"] = $name;
		if (!Library\Validate::int($options["zones"])) {
			throw new \InvalidArgumentException("Invalid number of editable zones supplied to Pages::registerPageType.");
		}
		if (!file_exists(Theme::getThemeDirectory() . "/" . $options["template"])) {
//			throw new InvalidArgumentException("The specified page template file does not exist.");
		}
		if ($options["icon"] !== NULL) {
			$type["icon"] = Theme::getThemeDirectoryURL() . "/" . $options["icon"];
		}
		if ($options["description"] !== NULL) {
			$type["description"] = $options["description"];
		}
		$numTypes = count(self::$pageTypes);
		self::$pageTypes[] = $type;
		return ($numTypes);
	}

	/**
	 * @return array
	 */
	public static function getPageTypes() {
		return self::$pageTypes;
	}

	/**
	 * @param string $identifier
	 *
	 * @return bool
	 */
	public static function pageTypeExists($identifier) {
		foreach (self::$pageTypes as $pageType) {
			if ($pageType["identifier"] == $identifier) {
				return true;
			}
		}
		return false;
	}

	/**
	 * @param string $identifier
	 *
	 * @return array
	 */
	public static function getPageType($identifier) {
		foreach (self::$pageTypes as $pageType) {
			if ($pageType["identifier"] == $identifier) {
				return $pageType;
			}
		}
		throw new \OutOfBoundsException("Nonexistent page type identifier supplied to Pages::getPageType.");
	}

	/**
	 * @param int      $selectedPageID      The ID of the page to give the selected attribute to
	 * @param int|bool $ignoreRoot          To disable a certain page and its descendants, pass its ID in here
	 *
	 * @return string
	 */
	public static function getPageHierarchySelectList($selectedPageID, $ignoreRoot = false) {
		return self::getPageHierarchySelectListRecursive($selectedPageID, Page::getTopLevelWithoutContent(), $ignoreRoot);
	}

	private static function getPageHierarchySelectListRecursive($selectedPageID, array $pageSet, $ignoreRoot = false, $prefix = "") {
		if ($pageSet == array()) {
			return "";
		}
		$string = "";
		foreach ($pageSet as $page) {

			$atts = "";

			// select the proper page
			if ($selectedPageID == $page->getID()) {
				$atts .= " selected";
			}
			// if we're disabling a page and all its descendants, do it here
			if ($ignoreRoot === true || $ignoreRoot === $page->getID()) {
				$atts .= " disabled";
				$ignoreRoot = true;
			}

			// include the prefix for nesting-formatting purposes
			$labelPrefix = $prefix;
			if ( $prefix !== "" ) {
				$labelPrefix .= "&nbsp; ";
			}

			// include this page in the list of options
			$string .= '<option value="' . $page->getID() . '"' . $atts . '>' . $labelPrefix . $page->getTitle() . '</option>' . PHP_EOL;

			// include this page's children in the list of options
			$string .= self::getPageHierarchySelectListRecursive( $selectedPageID, $page->getChildren(), $ignoreRoot, "––" . $prefix );

		}

		return $string;
	}

	/**
	 * @return string
	 */
	public static function getPageHierarchyTableList() {
		return self::getPageHierarchyTableListRecursive(Page::getTopLevelWithoutContent());
	}

	private static function getPageHierarchyTableListRecursive(array $pageSet, $prefix = "") {
		if ($pageSet == array()) {
			return "";
		}
		$string = "";
		foreach ($pageSet as $page) {
			$labelPrefix = "";
			if ($prefix !== "") {
				$labelPrefix = '<span class="child-indent">' . $prefix . '</span> ';
			}

			if ($page->getVisibility() == Page::VISIBILITY_PRIVATE) {
				$labelPrefix .= '<i class="fa fa-lock" title="Private page" style="margin-right: 0.35em;"></i>';
			} else if ($page->getVisibility() == Page::VISIBILITY_SECRET) {
				$labelPrefix .= '<i class="fa fa-user-secret" title="Secret page" style="margin-right: 0.35em;"></i>';
			}

			$string .= '<tr>' . PHP_EOL;

			$string .= '<td>' . $labelPrefix . '<a href="' . $page->getURL() . '">' . $page->getTitle() . '</a></td>' . PHP_EOL;

			$string .= '<td>' . $page->getShortname() . '</td>' . PHP_EOL;

			$string .= '<td class="actions-list">';
			$string .= '<a href="' . Site::getBaseURL() . '?edit=' . $page->getID() . '&settings" title="' . Localization::getLocalizedString(Localization::LABEL_PAGE_SETTINGS) . '"><i class="fa fa-' . Localization::getLocalizedString(Localization::ICON_PAGE_SETTINGS) . '"></i></a>';
			$string .= '<a href="' . Site::getBaseURL() . '?edit=' . $page->getID() . '" title="' . Localization::getLocalizedString(Localization::LABEL_EDIT_PAGE) . '"><i class="fa fa-' . Localization::getLocalizedString(Localization::ICON_EDIT_PAGE) . '"></i></a>';
			if ($page->isHomepage()) {
				$string .= '<a disabled title="' . Localization::getLocalizedString(Localization::LABEL_HOMEPAGE_NO_DELETE) . '"><i class="fa fa-' . Localization::getLocalizedString(Localization::ICON_DELETE_PAGE) . '"></i></a>';
			} else {
				$string .= '<a href="' . Site::getBaseURL() . '?delete=' . $page->getID() . '" title="' . Localization::getLocalizedString(Localization::LABEL_DELETE_PAGE) . '"><i class="fa fa-' . Localization::getLocalizedString(Localization::ICON_DELETE_PAGE) . '"></i></a>';
			}
			$string .= '</td>' . PHP_EOL;
			$string .= '</tr>' . PHP_EOL;

			$string .= self::getPageHierarchyTableListRecursive($page->getChildren(), "––" . $prefix);
		}

		return $string;
	}

	/**
	 * @param string $slug
	 *
	 * @return bool
	 */
	public static function slugExists($slug) {
		try {
			$pdo  = DB::getHandle();
			$stmt = $pdo->prepare("SELECT COUNT(*) FROM pages WHERE slug = :slug");
			$stmt->bindParam(":slug", $slug);
			$stmt->execute();
			$result = $stmt->fetch();
			if ($result === false) {
				throw new \PDOException();
			}
			if (isset($result[0])) {
				if ($result[0]  == 0) {
					return false;
				}
				return true;
			}
			return false;
		} catch (\PDOException $e) {
			throw new \InvalidArgumentException("Unable to check if slug already exists.");
		}
	}

}
