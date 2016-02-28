<?php

namespace CMS;

abstract class Plugin implements \JsonSerializable {

	protected static $pluginName;
	protected static $pluginVersion;
	private static $totalInstances = 0;
	private $instanceNumber;

	protected static final function getPluginName() {
		return self::$pluginName;
	}

	public final function __construct() {
		$this->initialize();
	}

	protected abstract function initialize();

	protected static final function setPluginName($name) {
		if (Library\Validate::plainText($name)) {
			self::$pluginName = $name;
		} else {
			throw new \InvalidArgumentException("Invalid plugin name supplied as argument.");
		}
	}

	protected static final function getPluginVersion() {
		return self::$pluginVersion;
	}

	protected static final function setPluginVersion($version) {
		if (Library\Validate::plainText($version)) {
			self::$pluginVersion = $version;
		} else {
			throw new \InvalidArgumentException("Invalid plugin version value supplied as argument.");
		}
	}

	public final function jsonSerialize() {
		return json_encode($this->asValuesArray(), JSON_PRETTY_PRINT);
	}

	abstract protected function getValuesAsArray();

	public final function asValuesArray() {
		$values = $this->getValuesAsArray();
		$class = get_class($this);
		$parts = explode("\\", $class);
		$class = end($parts);
		$values["plugin"] = $class;
		return $values;
	}

	abstract protected function setValuesWithArray(array $values);

	public static final function withValuesArray(array $values) {
		$pluginClass = "\\CMS\\Plugin\\" . $values["plugin"];
		$plugin = new $pluginClass;
		$plugin->setValuesWithArray($values);
		return $plugin;
	}
	
	public final function getPluginInstanceNumber() {
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

	abstract public function getPublicVersion();

	abstract public function getEditableVersion();

}