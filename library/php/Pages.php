<?php

class Pages {

	private static $currentPage;
	private static $pageTypes = array(
		0 => array(
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
			throw new BadMethodCallException("The current page is already set.");
		}
		self::$currentPage = $currentPage;
	}

	public static function registerPageType($name, array $options = array()) {
		$defaults = array(
			"icon" => NULL,
			"description" => NULL,
			"zones" => 1,
			"template" => "page.php",
		);
		$options = array_merge($defaults, $options);

		$type = array();
		$type["name"] = $name;
		if (!Validate::int($options["zones"])) {
			throw new InvalidArgumentException("Invalid number of editable zones supplied to Pages::registerPageType.");
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
			if ( $selectedPageID == $page->getID() ) {
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

			$string .= '<tr>' . PHP_EOL;

			$string .= '<td>' . $labelPrefix . '<a href="' . $page->getURL() . '">' . $page->getTitle() . '</a></td>' . PHP_EOL;

			$string .= '<td>' . $page->getShortname() . '</td>' . PHP_EOL;

			$string .= '<td class="actions-list">';
			$string .= '<a href="' . Site::getBaseURL() . '?edit=' . $page->getID() . '&redirect=pages" title="' . LABEL_EDIT_PAGE . '"><i class="fa fa-' . ICON_EDIT_PAGE . '"></i></a>';
			if ($page->isHomepage()) {
				$string .= '<a disabled title="' . LABEL_HOMEPAGE_NO_DELETE . '"><i class="fa fa-' . ICON_DELETE_PAGE . '"></i></a>';
			} else {
				$string .= '<a href="' . Site::getBaseURL() . '?delete=' . $page->getID() . '" title="' . LABEL_DELETE_PAGE . '"><i class="fa fa-' . ICON_DELETE_PAGE . '"></i></a>';
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
				throw new PDOException();
			}
			if (isset($result[0])) {
				if ($result[0]  == 0) {
					return false;
				}
				return true;
			}
			return false;
		} catch (PDOException $e) {
			throw new InvalidArgumentException("Unable to check if slug already exists.");
		}
	}

}
