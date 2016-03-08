<?php

namespace CMS\Plugin;
use CMS;

class Heading extends CMS\Plugin {

	protected static $pluginName = "Heading";
	protected static $pluginVersion = "1.0.0";

	private static $headingLevels = [1, 2, 3, 4, 5, 6];

	private $headingLevel;
	private $content;

	public function getLevel(): int {
		return $this->headingLevel;
	}

	public function setLevel(int $level) {
		if ($level < 1 || $level > 6) {
			throw new \InvalidArgumentException("Heading level must be a number from 1 to 6.");
		}
		$this->headingLevel = $level;
	}

	public function getContent(): string {
		return $this->content;
	}

	public function setContent(string $content) {
		if (CMS\Library\Validate::plainText($content, true)) {
			$this->content = $content;
		} else {
			throw new \InvalidArgumentException("Heading contains invalid plain text content.");
		}
	}

	protected function getValuesAsArray(): array {
		return [
			"pluginVersion" => self::$pluginVersion,
			"headingLevel" => $this->headingLevel,
			"content" => $this->content,
		];
	}

	protected function setValuesWithArray(array $values) {
		$this->setLevel($values["headingLevel"]);
		$this->setContent($values["content"]);
	}

	public function getPublicVersion(): string {
		return "<h" . $this->headingLevel . ">" . $this->content . "</h" . $this->headingLevel . ">";
	}

	public function getEditableVersion(): string {
		$instanceNumber = $this->getPluginInstanceNumber();
		$string = '<div class="heading-form-container heading-level-' . $this->headingLevel . '">' . PHP_EOL;
		$string .= '<select name="heading-' . $instanceNumber . '-level" class="cms-select">' . PHP_EOL;
		foreach (self::$headingLevels as $level) {
			$string .= '<option value="' . $level . '"' . (($level == $this->headingLevel) ? ' selected' : '') . '>H' . $level . '</option>' . PHP_EOL;
		}
		$string .= '</select>' . PHP_EOL;
		$string .= '<input type="text" name="heading-' . $instanceNumber . '-content" class="cms-input-invisible" value="' . $this->content . '">' . PHP_EOL;
		$string .= '</div>' . PHP_EOL;
		return $string;
	}
}