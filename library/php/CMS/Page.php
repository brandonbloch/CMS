<?php

namespace CMS;

class Page extends ActiveRecordAbstract {


	private $parentID;
	private $title;
	private $shortname;
	private $slug;
	private $content;

	const HOME_SLUG = "home";

	private function __construct($parentID, $title, $shortname, $slug, $content) {
		$this->setParentID($parentID);
		$this->setTitle($title);
		$this->setShortname($shortname);
		$this->setSlug($slug);
		$this->setContent($content);
	}

	/**
	 * @param array $row
	 *
	 * @return Page
	 */
	private static function withDatabaseRecord(array $row) {
		if (!isset($row["id"])) {
			throw new \InvalidArgumentException("Missing page ID from constructor.");
		}
		if (!isset($row["parent"])) {
			$row["parent"] = 0;
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
			$row["content"] = "";
		}
		$temp = new self($row["parent"], $row["title"], $row["short_title"], $row["slug"], $row["content"]);
		$temp->setID($row["id"]);
		return $temp;
	}

	/**
	 * @param int $id
	 *
	 * @return Page
	 */
	public static function withID($id) {
		if (!Validate::int($id)) {
			throw new \InvalidArgumentException("Page::withID expected int, got " . gettype($id) . " instead.");
		}
		$id = (int) $id;
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
	public static function withSlug($slug) {
		if (!Validate::plainText($slug)) {
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
	public static function getAllWithoutContent() {
		try {
			$pdo    = DB::getHandle();
			$stmt   = $pdo->query("SELECT id, parent, title, short_title, slug FROM pages");
			$result = $stmt->fetchAll();
			if ($result === false) {
				throw new \PDOException();
			}
			$pages = array();
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
	public static function getTopLevelWithoutContent() {
		try {
			$pdo    = DB::getHandle();
			$stmt   = $pdo->query("SELECT id, title, short_title, slug FROM pages WHERE parent = 0");
			$result = $stmt->fetchAll();
			if ($result === false) {
				throw new \PDOException();
			}
			$pages = array();
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
	public static function getHomepage() {
		return self::withSlug(self::HOME_SLUG);
	}

	/**
	 * @return Page
	 */
	public static function create($parentID, $title, $shortname, $slug) {
		$page = new self($parentID, $title, $shortname, $slug, "");
		$success = $page->insert();
		if (!$success) {
			throw new \RuntimeException("Unable to save new page to the database.");
		}
		return $page;
	}

	protected function insert() {
		try {
			$pdo  = DB::getHandle();
			$stmt = $pdo->prepare("INSERT INTO pages (id, parent, title, short_title, slug, content) VALUES (NULL, :parent, :title, :short_title, :slug, :content)");
			$stmt->bindParam(":parent", $this->parentID);
			$stmt->bindParam(":title", $this->title);
			$stmt->bindParam(":short_title", $this->shortname);
			$stmt->bindParam(":slug", $this->slug);
			$stmt->bindParam(":content", $this->content);
			$stmt->execute();
			$this->setID($pdo->lastInsertId());
			return true;
		} catch (\PDOException $e) {
			return false;
		}
	}

	protected function update() {
		if (!$this->id) {
			throw new BadMethodCallException("Attempt to update nonexistent page.");
		}
		try {
			$pdo  = DB::getHandle();
			$stmt = $pdo->prepare("UPDATE pages SET parent = :parent, title = :title, short_title = :short_title, slug = :slug, content = :content WHERE id = :id");
			$stmt->bindParam(":parent", $this->parentID);
			$stmt->bindParam(":title", $this->title);
			$stmt->bindParam(":short_title", $this->shortname);
			$stmt->bindParam(":slug", $this->slug);
			$stmt->bindParam(":content", $this->content);
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
	public function delete() {
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
	public function getParentID() {
		return $this->parentID;
	}

	/**
	 * @return Page
	 */
	public function getParent() {
		return self::withID($this->parentID);
	}

	public function setParentID($id) {
		if (!Validate::int($id)) {
			throw new \InvalidArgumentException("setParentID expected int, got " . gettype($id) . " instead.");
		}
		$this->parentID = (int) $id;
	}

	/**
	 * @return int[]
	 */
	public function getChildrenIDs() {
		try {
			$pdo  = DB::getHandle();
			$stmt = $pdo->prepare("SELECT id FROM pages WHERE parent = :id");
			$stmt->bindParam(":id", $this->id);
			$stmt->execute();
			$result = $stmt->fetchAll();
			if ($result === false) {
				throw new \PDOException();
			}
			$childIDs = array();
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
	public function getChildren() {
		try {
			$pdo  = DB::getHandle();
			$stmt = $pdo->prepare("SELECT id, parent, title, short_title, slug FROM pages WHERE parent = :id");
			$stmt->bindParam(":id", $this->id);
			$stmt->execute();
			$result = $stmt->fetchAll();
			if ($result === false) {
				throw new \PDOException();
			}
			$childIDs = array();
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
	public function getDescendants() {
		return $this->getDescendantsRecursive($this->getChildren());
	}

	private function getDescendantsRecursive(array $pageSet) {
		$descendants = array();
		foreach ($pageSet as $page) {
			$descendants[] = $page;
			$descendants = array_merge($descendants, $page->getChildren());
		}
		return $descendants;
	}

	/**
	 * @return bool
	 */
	public function isHomepage() {
		return ($this->slug == self::HOME_SLUG) ? true : false;
	}

	/**
	 * @return string
	 */
	public function getURL() {
		if ($this->isHomepage()) {
			return Site::getBaseURL();
		}
		// TODO if there are options for permalink structure, they will need to be checked here
		return Site::getBaseURL() . "/" . $this->slug;
//		return Site::getBaseURL() . "?slug=" . $this->slug;
//		return Site::getBaseURL() . "?id=" . $this->id;
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title) {
		if (!Validate::plainText($title, true)) {
			throw new \InvalidArgumentException("Invalid string content supplied to setTitle.");
		}
		$this->title = $title;
	}

	/**
	 * @return string
	 */
	public function getShortname() {
		return $this->shortname;
	}

	/**
	 * @param string $shortname
	 */
	public function setShortname($shortname) {
		if (!Validate::plainText($shortname)) {
			throw new \InvalidArgumentException("Invalid string content supplied to setShortname.");
		}
		$this->shortname = $shortname;
	}

	/**
	 * @return string
	 */
	public function getSlug() {
		return $this->slug;
	}

	/**
	 * @param string $slug
	 */
	public function setSlug($slug) {
		if (!Validate::slug($slug)) {
			throw new \InvalidArgumentException("Invalid string content supplied to setSlug.");
		}
		$this->slug = $slug;
	}

	/**
	 * @return string
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * @param string $content
	 */
	public function setContent($content) {
		if (!Validate::plainText($content, true)) {
			throw new \InvalidArgumentException("Invalid string content supplied to setContent.");
		}
		$this->content = $content;
	}

}