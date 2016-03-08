<?php

namespace CMS\Plugin;
use CMS;

class Textarea extends CMS\Plugin {

	protected static $pluginName = "Text Area";
	protected static $pluginVersion = "1.0.0";

	private $content;

	public function getContent(): string {
		return $this->content;
	}

	public function setContent(string $content) {
		if (CMS\Library\Validate::HTML($content, true)) {
			$this->content = $content;
		} else {
			throw new \InvalidArgumentException("Text Area contains invalid content.");
		}
	}

	protected function getValuesAsArray(): array {
		return [
			"pluginVersion" => self::$pluginVersion,
			"content" => $this->content,
		];
	}

	protected function setValuesWithArray(array $values) {
		$this->setContent($values["content"]);
	}

	public function getPublicVersion(): string {
		return CMS\Library\Markdown::parse($this->content);
	}

	public function getEditableVersion(): string {
		$instanceNumber = $this->getPluginInstanceNumber();
		$string = '<div class="textarea-form-container">' . PHP_EOL;
		$string .= '<textarea name="textarea-' . $instanceNumber . '-content" class="cms-textarea-invisible">' . $this->content . '</textarea>' . PHP_EOL;
		$string .= '</div>' . PHP_EOL;
		return $string;
	}
}