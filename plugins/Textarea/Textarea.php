<?php

namespace CMS\Plugin;
use CMS;

class Textarea extends CMS\Plugin {

	protected static $pluginName = "Text Area";
	protected static $pluginVersion = "1.0.0";

	private $content;

	protected function initialize() {}

	public function getContent() {
		return $this->content;
	}

	public function setContent($content) {
		if (CMS\Library\Validate::HTML($content, true)) {
			$this->content = $content;
		} else {
			throw new \InvalidArgumentException("Text Area contains invalid content.");
		}
	}

	protected function getValuesAsArray() {
		$values = array(
			"pluginVersion" => self::$pluginVersion,
			"content" => $this->content,
		);
		return $values;
	}

	protected function setValuesWithArray(array $values) {
		$this->setContent($values["content"]);
	}

	public function getPublicVersion() {
		return CMS\Library\Markdown::parse($this->content);
	}

	public function getEditableVersion() {
		$instanceNumber = $this->getPluginInstanceNumber();
		$string = '<div class="textarea-form-container">' . PHP_EOL;
		$string .= '<textarea name="textarea-' . $instanceNumber . '-content" class="cms-textarea-invisible">' . $this->content . '</textarea>' . PHP_EOL;
		$string .= '</div>' . PHP_EOL;
		return $string;
	}
}