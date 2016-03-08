<?php

// TODO: start using PHP 7!

namespace CMS;

class Page extends ActiveRecordAbstract {

	private $parentID;
	private $pageType;
	private $visibility;
	private $title;
	private $shortname;
	private $slug;
	private $content;

	const HOME_SLUG = "home";

	const VISIBILITY_PUBLIC = 0;
	const VISIBILITY_PRIVATE = 1;
	const VISIBILITY_SECRET = 2;

	private static $visibilityDescriptions = [
		self::VISIBILITY_PUBLIC =>
			"Public pages are visible for all users on your site and are shown in navigation menus.",
		self::VISIBILITY_PRIVATE =>
			"Private pages are only visible to you while logged in. This option is perfect for working on a new page before publishing.",
		self::VISIBILITY_SECRET =>
			"Secret pages are visible to anyone with the page URL, but will not be shown in navigation menus.",
	];

	public static function getVisibilityDescriptions(): array {
		return self::$visibilityDescriptions;
	}

	private function __construct(string $pageType, int $parentID, int $visibility, string $title, string $shortname, string $slug, string $content) {
		$this->setPageType($pageType);
		$this->setParentID($parentID);
		$this->setVisibility($visibility);
		$this->setTitle($title);
		$this->setShortname($shortname);
		$this->setSlug($slug);
		$this->setContentWithJSON($content);
	}

	/**
	 * @param array $row
	 *
	 * @return Page
	 */
	private static function withDatabaseRecord(array $row): Page {
		if (!isset($row["id"])) {
			throw new \InvalidArgumentException("Missing page ID from constructor.");
		}
		if (!isset($row["page_type"])) {
			$row["page_type"] = Pages::PAGE_TYPE_DEFAULT;
		}
		if (!isset($row["parent"])) {
			$row["parent"] = 0;
		}
		if (!isset($row["visibility"])) {
			var_dump($row);
			throw new \InvalidArgumentException("Missing page visibility value from constructor.");
		}
		if (!isset($row["title"])) {
			$row["title"] = "";
		}
		if (!isset($row["short_title"])) {
			throw new \InvalidArgumentException("Missing page short title from constructor.");
		}
		if (!isset($row["slug"])) {
			throw new \InvalidArgumentException("Missing page slug from constructor.");
		}
		if (!isset($row["content"])) {
			$row["content"] = "[]";
		}
		$temp = new self($row["page_type"], $row["parent"], $row["visibility"], $row["title"], $row["short_title"], $row["slug"], $row["content"]);
		$temp->setID($row["id"]);
		return $temp;
	}

	/**
	 * @param int $id
	 *
	 * @return Page
	 */
	public static function withID(int $id): Page {
		if ($id <= 0) {
			throw new \OutOfRangeException("Page IDs must all be greater than 0.");
		}
		try {
			$pdo    = DB::getHandle();
			$stmt   = $pdo->prepare("SELECT * FROM pages WHERE id = :id");
			$stmt->bindParam("id", $id);
			$stmt->execute();
			$result = $stmt->fetch();
			if ($result === false) {
				throw new \PDOException();
			}
			return self::withDatabaseRecord($result);
		} catch (\PDOException $e) {
			throw new \RuntimeException("Unable to retrieve page from the database with ID " . $id . ".");
		}
	}

	/**
	 * @param string $slug
	 *
	 * @return Page
	 */
	public static function withSlug(string $slug): Page {
		if (!Library\Validate::plainText($slug)) {
			throw new \OutOfRangeException("Invalid slug supplied to Page::withSlug.");
		}
		try {
			$pdo    = DB::getHandle();
			$stmt   = $pdo->prepare("SELECT * FROM pages WHERE slug = :slug");
			$stmt->bindParam(":slug", $slug);
			$stmt->execute();
			$result = $stmt->fetch();
			if ($result === false) {
				throw new \PDOException();
			}
			return self::withDatabaseRecord($result);
		} catch (\PDOException $e) {
			throw new \RuntimeException("Unable to retrieve page from the database with slug '" . $slug . "'.");
		}
	}

	/**
	 * @return Page[]
	 */
	public static function getAllWithoutContent(): array {
		try {
			$pdo    = DB::getHandle();
			$stmt   = $pdo->query("SELECT id, parent, visibility, title, short_title, slug FROM pages");
			$result = $stmt->fetchAll();
			if ($result === false) {
				throw new \PDOException();
			}
			$pages = [];
			foreach ($result as $page) {
				$pages[$page["id"]] = self::withDatabaseRecord($page);
			}
			return $pages;
		} catch (\PDOException $e) {
			throw new \RuntimeException("Unable to retrieve page list from the database.");
		}
	}

	/**
	 * @return Page[]
	 */
	public static function getTopLevelWithoutContent(): array {
		try {
			$pdo    = DB::getHandle();
			$stmt   = $pdo->query("SELECT id, visibility, title, short_title, slug FROM pages WHERE parent = 0");
			$result = $stmt->fetchAll();
			if ($result === false) {
				throw new \PDOException();
			}
			$pages = [];
			foreach ($result as $page) {
				$page["parent"] = 0;
				$pages[$page["id"]] = self::withDatabaseRecord($page);
			}
			return $pages;
		} catch (\PDOException $e) {
			throw new \RuntimeException("Unable to retrieve top-level page list from the database.");
		}
	}

	/**
	 * @return Page
	 */
	public static function getHomepage(): Page {
		return self::withSlug(self::HOME_SLUG);
	}

	/**
	 * @param $pageType
	 * @param $parentID
	 * @param $visibility
	 * @param $title
	 * @param $shortname
	 * @param $slug
	 *
	 * @return Page
	 */
	public static function create(string $pageType, int $parentID, int $visibility, string $title, string $shortname, string $slug) {
		$page = new self($pageType, $parentID, $visibility, $title, $shortname, $slug, "[]");
		$success = $page->insert();
		if (!$success) {
			throw new \RuntimeException("Unable to save new page to the database.");
		}
		return $page;
	}

	protected function insert(): bool {
		if ($this->id) {
			throw new \BadMethodCallException("Attempt to insert existing page.");
		}
		$content = $this->getContentAsJSON();
		try {
			$pdo  = DB::getHandle();
			$stmt = $pdo->prepare("INSERT INTO pages (id, page_type, parent, visibility, title, short_title, slug, content) VALUES (NULL, :page_type, :parent, :visibility, :title, :short_title, :slug, :content)");
			$stmt->bindParam(":page_type", $this->pageType);
			$stmt->bindParam(":parent", $this->parentID);
			$stmt->bindParam(":visibility", $this->visibility);
			$stmt->bindParam(":title", $this->title);
			$stmt->bindParam(":short_title", $this->shortname);
			$stmt->bindParam(":slug", $this->slug);
			$stmt->bindParam(":content", $content);
			$stmt->execute();
			$this->setID($pdo->lastInsertId());
			return true;
		} catch (\PDOException $e) {
			return false;
		}
	}

	protected function update(): bool {
		if (!$this->id) {
			throw new \BadMethodCallException("Attempt to update nonexistent page.");
		}
		$content = $this->getContentAsJSON();
		try {
			$pdo  = DB::getHandle();
			$stmt = $pdo->prepare("UPDATE pages SET page_type = :page_type, parent = :parent, visibility = :visibility, title = :title, short_title = :short_title, slug = :slug, content = :content WHERE id = :id");
			$stmt->bindParam(":page_type", $this->pageType);
			$stmt->bindParam(":parent", $this->parentID);
			$stmt->bindParam(":visibility", $this->visibility);
			$stmt->bindParam(":title", $this->title);
			$stmt->bindParam(":short_title", $this->shortname);
			$stmt->bindParam(":slug", $this->slug);
			$stmt->bindParam(":content", $content);
			$stmt->bindParam(":id", $this->id);
			$stmt->execute();
			return true;
		} catch (\PDOException $e) {
			return false;
		}
	}

	/**
	 * @return bool
	 */
	public function delete(): bool {
		if (!$this->id) {
			throw new \BadMethodCallException("Attempt to delete nonexistent page.");
		}
		try {
			$pdo  = DB::getHandle();
			$stmt = $pdo->prepare("DELETE FROM pages WHERE id = :id");
			$stmt->bindParam(":id", $this->id);
			$stmt->execute();
			return true;
		} catch (\PDOException $e) {
			return false;
		}
	}

	/**
	 * @return int
	 */
	public function getParentID(): int {
		return $this->parentID;
	}

	/**
	 * @return string
	 */
	public function getPageTypeIdentifier(): string {
		return $this->pageType;
	}

	public function getPageTypeArray(): array {
		return Pages::getPageType($this->pageType);
	}

	public function setPageType(string $identifier) {
		if (Library\Validate::plainText($identifier)) {
			$this->pageType = $identifier;
		} else {
			throw new \InvalidArgumentException("Invalid page type identifier supplied to setPageType.");
		}
	}

	/**
	 * @return Page
	 */
	public function getParent(): Page {
		return self::withID($this->parentID);
	}

	/**
	 * @param int $id
	 */
	public function setParentID(int $id) {
		$this->parentID = (int) $id;
	}

	/**
	 * @return int[]
	 */
	public function getChildrenIDs(): array {
		try {
			$pdo  = DB::getHandle();
			$stmt = $pdo->prepare("SELECT id FROM pages WHERE parent = :id");
			$stmt->bindParam(":id", $this->id);
			$stmt->execute();
			$result = $stmt->fetchAll();
			if ($result === false) {
				throw new \PDOException();
			}
			$childIDs = [];
			foreach ($result as $row) {
				$childIDs[] = (int) $row;
			}
			return $childIDs;
		} catch (\PDOException $e) {
			throw new \RuntimeException("Unable to retrieve child page ID list from the database.");
		}
	}

	/**
	 * @return Page[]
	 */
	public function getChildren(): array {
		try {
			$pdo  = DB::getHandle();
			$stmt = $pdo->prepare("SELECT id, parent, visibility, title, short_title, slug FROM pages WHERE parent = :id");
			$stmt->bindParam(":id", $this->id);
			$stmt->execute();
			$result = $stmt->fetchAll();
			if ($result === false) {
				throw new \PDOException();
			}
			$childIDs = [];
			foreach ($result as $row) {
				$childIDs[] = self::withDatabaseRecord($row);
			}
			return $childIDs;
		} catch (\PDOException $e) {
			throw new \RuntimeException("Unable to retrieve child page list from the database.");
		}
	}

	/**
	 * @return int[]
	 */
	public function getPublicChildrenIDs(): array {
		$visibility = self::VISIBILITY_PUBLIC;
		try {
			$pdo  = DB::getHandle();
			$stmt = $pdo->prepare("SELECT id FROM pages WHERE parent = :id AND visibility = :public");
			$stmt->bindParam(":id", $this->id);
			$stmt->bindParam(":public", $visibility);
			$stmt->execute();
			$result = $stmt->fetchAll();
			if ($result === false) {
				throw new \PDOException();
			}
			$childIDs = [];
			foreach ($result as $row) {
				$childIDs[] = (int) $row;
			}
			return $childIDs;
		} catch (\PDOException $e) {
			throw new \RuntimeException("Unable to retrieve child page ID list from the database.");
		}
	}

	/**
	 * @return Page[]
	 */
	public function getPublicChildren(): array {
		$visibility = self::VISIBILITY_PUBLIC;
		try {
			$pdo  = DB::getHandle();
			$stmt = $pdo->prepare("SELECT id, parent, visibility, title, short_title, slug FROM pages WHERE parent = :id AND visibility = :public");
			$stmt->bindParam(":id", $this->id);
			$stmt->bindParam(":public", $visibility);
			$stmt->execute();
			$result = $stmt->fetchAll();
			if ($result === false) {
				throw new \PDOException();
			}
			$childIDs = [];
			foreach ($result as $row) {
				$childIDs[] = self::withDatabaseRecord($row);
			}
			return $childIDs;
		} catch (\PDOException $e) {
			throw new \RuntimeException("Unable to retrieve child page list from the database.");
		}
	}

	/**
	 * @return Page[]
	 */
	public function getDescendants(): array {
		return $this->getDescendantsRecursive($this->getChildren());
	}

	private function getDescendantsRecursive(array $pageSet): array {
		$descendants = [];
		foreach ($pageSet as $page) {
			$descendants[] = $page;
			$descendants = array_merge($descendants, $page->getChildren());
		}
		return $descendants;
	}

	/**
	 * @return bool
	 */
	public function isHomepage(): bool {
		return ($this->slug == self::HOME_SLUG) ? true : false;
	}

	/**
	 * @return string
	 */
	public function getURL(): string {
		if ($this->isHomepage()) {
			return Site::getBaseURL();
		}
		// TODO if there are options for permalink structure, they will need to be checked here
		return Site::getBaseURL() . "/" . $this->slug;
//		return Site::getBaseURL() . "?slug=" . $this->slug;     // set the URL using the page slug as a parameter
//		return Site::getBaseURL() . "?id=" . $this->id;         // set the URL using the page ID as a parameter
	}

	public function getVisibility(): int {
		return $this->visibility;
	}

	public function setVisibility(int $visibility) {
		if (!array_key_exists($visibility, self::getVisibilityDescriptions())) {
		throw new \InvalidArgumentException("Nonexistent page visibility option supplied to setVisibility.");
		}
		$this->visibility = $visibility;
	}

	/**
	 * @return string
	 */
	public function getTitle(): string {
		return $this->title;
	}

	/**
	 * @param string $title
	 */
	public function setTitle(string $title) {
		if (!Library\Validate::plainText($title, true)) {
			throw new \InvalidArgumentException("Invalid string content supplied to setTitle.");
		}
		$this->title = $title;
	}

	/**
	 * @return string
	 */
	public function getShortname(): string {
		return $this->shortname;
	}

	/**
	 * @param string $shortname
	 */
	public function setShortname(string $shortname) {
		if (!Library\Validate::plainText($shortname)) {
			throw new \InvalidArgumentException("Invalid string content supplied to setShortname.");
		}
		$this->shortname = $shortname;
	}

	/**
	 * @return string
	 */
	public function getSlug(): string {
		return $this->slug;
	}

	/**
	 * @param string $slug
	 */
	public function setSlug(string $slug) {
		if (!Library\Validate::slug($slug)) {
			throw new \InvalidArgumentException("Invalid string content supplied to setSlug.");
		}
		$this->slug = $slug;
	}

	public function getContentAsJSON(): string {
		$content = [];
		foreach ($this->content as $zone) {
			$zoneArray = [];
			foreach ($zone as $plugin) {
				/** @var Plugin $plugin */
				$zoneArray[] = $plugin->asValuesArray();
			}
			$content[] = $zoneArray;
		}
		return json_encode($content, JSON_PRETTY_PRINT);
	}

	/**
	 * @param string $json
	 */
	public function setContentWithJSON(string $json) {
		$content = [];
		$array = json_decode($json, true);
		// check for JSON errors
		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new \InvalidArgumentException("Invalid JSON supplied to setContentWithJSON.");
		}
		// first level of the array is the number of editable zones
		foreach ($array as $zone => $plugins) {
			$content[$zone] = [];
			// second level of the array is the list of plugins
			foreach ($array[$zone] as $plugin => $values) {
				try {
					$content[$zone][$plugin] = Plugin::withValuesArray($values);
				} catch (\InvalidArgumentException $e) {
					// TODO create a fallback plugin representing this object,
					// which only shows up to display an error message on the edit page
				}
			}
		}
		// if no editable zones were set, create one
		if (count($content) == 0) {
			$content[] = [];
		}
		$this->content = $content;
	}

	public function getZoneOutput(int $zone): string {
		$output = '<div class="cms-zone cms-zone-' . $zone . '">' . PHP_EOL;
		if (isset($_GET["edit"]) && !isset($_GET["settings"])) {
			$output .= $this->getEditableZoneOutput($zone);
		} else {
			$output .= $this->getPublicZoneOutput($zone);
		}
		$output .= '</div>' . PHP_EOL;
		return $output;
	}

	private function getPublicZoneOutput(int $zone): string {
		if (!array_key_exists($zone, $this->content)) {
			return "";
		}
		$output = "";
		foreach ($this->content[$zone] as $plugin) {
			/** @var Plugin $plugin */
			$output .= $plugin->getPublicVersion() . PHP_EOL;
		}
		return $output;
	}

	private function getEditableZoneOutput(int $zone): string {
		if (!array_key_exists($zone, $this->content)) {
			return "";
		}
		$output = "";
		$output .= '<link rel="stylesheet" property="stylesheet" href="' . Site::getBaseURL() . '/library/css/plugindefaults.css">' . PHP_EOL;
		foreach ($this->content[$zone] as $plugin) {
			/** @var Plugin $plugin */
			$output .= '<div class="cms-plugin-container">' . PHP_EOL;
			$stylesheet = $plugin->getEditableStylesheet();
			if ($stylesheet) {
				$output .= '<link rel="stylesheet" property="stylesheet" href="' . $stylesheet . '">' . PHP_EOL;
			}
			$output .= $plugin->getEditableVersion() . PHP_EOL;
			$output .= '</div>' . PHP_EOL;
		}
		return $output;
	}

}