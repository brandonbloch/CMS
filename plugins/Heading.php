<?php

namespace CMS\Plugin;
use CMS;

class Heading extends CMS\Plugin {

	protected static $pluginName = "Heading";
	protected static $pluginVersion = "1.0.0";

	private static $headingLevels = array(1, 2, 3, 4, 5, 6);

	private $headingLevel;
	private $content;

	public function __construct($level, $content) {
		$this->setLevel($level);
		$this->setContent($content);
	}

	public function getLevel() {
		return $this->headingLevel;
	}

	public function setLevel($level) {
		if (!CMS\Library\Validate::int($level)) {
			if (is_string($level) && ctype_digit($level)) {
				$level = (int) $level;
			} else {
				throw new \InvalidArgumentException("Expected int for heading level, got " . gettype($level) . " instead.");
			}
		}
		if ($level < 1 || $level > 6) {
			throw new \InvalidArgumentException("Heading level must be a number from 1 to 6.");
		}
		$this->headingLevel = $level;
	}

	public function getContent() {
		return $this->content;
	}

	public function setContent($content) {
		if (CMS\Library\Validate::plainText($content, true)) {
			$this->content = $content;
		} else {
			throw new \InvalidArgumentException("Heading contains invalid plain text content.");
		}
	}

	public function __toString() {
		return "<h" . $this->headingLevel . ">" . $this->content . "</h" . $this->headingLevel . ">";
	}
}