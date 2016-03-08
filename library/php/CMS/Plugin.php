<?php

namespace CMS;

abstract class Plugin implements \JsonSerializable {

	protected static $pluginName;
	protected static $pluginVersion;
	private static $totalInstances = 0;
	private $instanceNumber;

	protected static final function getPluginName(): string {
		return self::$pluginName;
	}

	public final function __construct() {
		$this->initialize();
	}

	// plugins that need to perform additional setup in their constructor should override this method
	protected function initialize() {}

	protected static final function setPluginName(string $name) {
		if (!Library\Validate::plainText($name)) {
			throw new \InvalidArgumentException("Invalid plugin name supplied as argument.");
		}
		self::$pluginName = $name;
	}

	protected static final function getPluginVersion(): string {
		return self::$pluginVersion;
	}

	protected static final function setPluginVersion(string $version) {
		if (!Library\Validate::plainText($version)) {
			throw new \InvalidArgumentException("Invalid plugin version value supplied as argument.");
		}
		self::$pluginVersion = $version;
	}

	public final function jsonSerialize(): string {
		return json_encode($this->asValuesArray(), JSON_PRETTY_PRINT);
	}

	abstract protected function getValuesAsArray(): array;

	public final function asValuesArray(): array {
		$values = $this->getValuesAsArray();
		$class = get_class($this);
		$parts = explode("\\", $class);
		$class = end($parts);
		$values["plugin"] = $class;
		return $values;
	}

	abstract protected function setValuesWithArray(array $values);

	public static final function withValuesArray(array $values): Plugin {
		$pluginClass = "\\CMS\\Plugin\\" . $values["plugin"];
		$plugin = new $pluginClass;
		/** @var Plugin $plugin */
		$plugin->setValuesWithArray($values);
		return $plugin;
	}
	
	public final function getPluginInstanceNumber(): int {
		if (!$this->instanceNumber) {
			Plugin::$totalInstances++;
			$this->instanceNumber = Plugin::$totalInstances;
		}
		return $this->instanceNumber;
	}

	public final function getEditableStylesheet() {
		$class = get_class($this);
		$parts = explode("\\", $class);
		$class = end($parts);
		if (file_exists("plugins/" . $class . "/editable.css")) {
			return Site::getBaseURL() . "/plugins/" . $class . "/editable.css";
		}
		return false;
	}

	abstract public function getPublicVersion(): string;

	abstract public function getEditableVersion(): string;

}