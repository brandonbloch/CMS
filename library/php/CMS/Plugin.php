<?php

namespace CMS;

abstract class Plugin implements PluginInterface {

	protected static $pluginName;
	protected static $pluginVersion;

	protected static final function getPluginName() {
		return self::$pluginName;
	}

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

	public abstract function __toString();

}