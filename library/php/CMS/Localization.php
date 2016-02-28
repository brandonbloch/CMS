<?php

namespace CMS;

class Localization {

	const LABEL_HOME = 0;
	const LABEL_MANAGE_PAGES = 1;
	const LABEL_ADD_PAGE = 2;
	const LABEL_EDIT_PAGE = 3;
	const LABEL_EDIT_SAVE = 4;
	const LABEL_EDIT_CANCEL = 5;
	const LABEL_DELETE_PAGE = 6;
	const LABEL_HOMEPAGE_NO_DELETE = 7;
	const LABEL_PAGE_SETTINGS = 8;
	const LABEL_SETTINGS = 9;
	const LABEL_LOGOUT = 10;
	const LABEL_403 = 11;
	const LABEL_404 = 12;

	const BUTTON_CREATE = 20;
	const BUTTON_SAVE = 21;
	const BUTTON_DELETE = 22;

	const ICON_HOME = 40;
	const ICON_MANAGE_PAGES = 41;
	const ICON_ADD_PAGE = 42;
	const ICON_EDIT_PAGE = 43;
	const ICON_EDIT_SAVE = 44;
	const ICON_EDIT_CANCEL = 45;
	const ICON_DELETE_PAGE = 46;
	const ICON_PAGE_SETTINGS = 47;
	const ICON_SETTINGS = 48;
	const ICON_LOGOUT = 49;

	const DEFAULT_LANGUAGE = "English";

	private static $language = self::DEFAULT_LANGUAGE;

	private static $languageCodes = array(
		"English" => "en",
	);

	private static $localizationStrings = array(
		"English" => array(
			self::LABEL_HOME => "Home",
			self::LABEL_MANAGE_PAGES => "Manage Pages",
			self::LABEL_ADD_PAGE => "Create New Page",
			self::LABEL_EDIT_PAGE => "Edit Page",
			self::LABEL_EDIT_SAVE => "Save Changes",
			self::LABEL_EDIT_CANCEL => "Scrap Changes",
			self::LABEL_DELETE_PAGE => "Delete Page",
			self::LABEL_HOMEPAGE_NO_DELETE => "The homepage cannot be deleted",
			self::LABEL_PAGE_SETTINGS => "Page Settings",
			self::LABEL_SETTINGS => "Preferences",
			self::LABEL_LOGOUT => "Log Out",
			self::LABEL_403 => "Forbidden",
			self::LABEL_404 => "Not Found",
			self::BUTTON_CREATE => "Create",
			self::BUTTON_SAVE => "Save",
			self::BUTTON_DELETE => "Delete",
			self::ICON_HOME => "home",
			self::ICON_MANAGE_PAGES => "bars",
			self::ICON_ADD_PAGE => "plus",
			self::ICON_EDIT_PAGE => "arrows",
			self::ICON_EDIT_SAVE => "check",
			self::ICON_EDIT_CANCEL => "times",
			self::ICON_DELETE_PAGE => "trash-o",
			self::ICON_PAGE_SETTINGS => "wrench",
			self::ICON_SETTINGS => "gear",
			self::ICON_LOGOUT => "sign-out",
		),
	);

	public static function getLanguageCode() {
		return self::$languageCodes[self::$language];
	}

	public static function setLanguageCode($language) {
		if (array_key_exists($language, self::$languageCodes)) {
			self::$language = $language;
		} else {
			throw new \InvalidArgumentException("No localization data exists for the language '" . $language . "'.");
		}
	}

	public static function getLocalizedString($identifier) {
		if (array_key_exists($identifier, self::$localizationStrings[self::$language])) {
			return self::$localizationStrings[self::$language][$identifier];
		} else if (array_key_exists($identifier, self::$localizationStrings[self::DEFAULT_LANGUAGE])) {
			return self::$localizationStrings[self::DEFAULT_LANGUAGE][$identifier];
		} else {
			throw new \OutOfBoundsException("Nonexistent localized string identifier '" . $identifier . "' supplied as argument.");
		}
	}

	public static function registerLocalization($language, $languageCode, array $localizationStrings) {
		if (!is_string($language)) {
			throw new \InvalidArgumentException("Localization::registerLocalization expected string for language name, got '" . gettype($language) . "' instead.");
		} else if (!is_string($languageCode)) {
			throw new \InvalidArgumentException("Localization::registerLocalization expected string for language code, got '" . gettype($languageCode) . "' instead.");
		} else if (array_key_exists($language, self::$languageCodes)) {
			throw new \InvalidArgumentException("The language '" . $language . "' already has localization data.");
		}
		if (count($localizationStrings) == 0) {
			throw new \InvalidArgumentException("Empty localization string array supplied as argument.");
		}
		self::$languageCodes[$language] = $languageCode;
		self::$localizationStrings[$language] = $localizationStrings;
	}

}