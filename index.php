<?php

// get the configuration options
require_once "configuration.php";

// include the library and plugins directories via class auto-loading
spl_autoload_register(function($class) {
	$class = str_replace("\\", "/", $class);
	if (file_exists("library/php/$class.php")) {
		include_once "library/php/$class.php";
	} else {
		$parts = explode("/", $class);
		$class = end($parts);
		if (file_exists("plugins/$class/$class.php")) {
			include_once "plugins/$class/$class.php";
		}
	}
});



mb_language(\CMS\Localization::getLanguageCode());
mb_regex_encoding("UTF-8");

// Error handlers

// 404 page was redirected to this page by .htaccess
if (isset($_GET["404"])) {
	CMS\Site::set404Response();
}
// 403 page was redirected to this page by .htaccess
if (isset($_GET["403"])) {
	\CMS\Site::set403Response();
}

// begin main pageloader action

if (file_exists(CMS\Theme::getThemeDirectory() . "/theme.php")) {
	include_once CMS\Theme::getThemeDirectory() . "/theme.php";
}

// logout process requested
if (isset($_GET["logout"])) {
	include "actions/logout.php";
	die();
}

// page requested by slug or ID
if (isset($_GET["slug"]) || isset($_GET["id"])) {
	if (isset($_GET["slug"])) {
		// if the page slug is nonexistent, send a 404 response
		try {
			$currentPage = CMS\Page::withSlug($_GET["slug"]);
		} catch (Exception $e) {
			CMS\Site::set404Response();
		}
	} else if (isset($_GET["id"])) {
		// if the page ID is invalid or nonexistent, send a 404 response
		try {
			$currentPage = CMS\Page::withID($_GET["id"]);
		} catch (Exception $e) {
			CMS\Site::set404Response();
		}
	}
	// if on the homepage, redirect Site::getBaseURL (this will run again and reach the bottom)
	if ($currentPage->isHomepage()) {
		CMS\Browser::redirect(CMS\Site::getBaseURL());
	}
	// try loading the page type's template, or fallback to default
	CMS\Pages::setCurrentPage($currentPage);
	$templateFile = CMS\Theme::getTemplateForPageType($currentPage->getPageTypeIdentifier());
	include CMS\Theme::getThemeDirectory() . "/" . $templateFile;
	die();
}

// delete page requested by page ID
if (isset($_GET["delete"])) {
	include "actions/pagedelete.php";
	die();
}

// edit page requested by page ID
if (isset($_GET["edit"])) {
	if (isset($_GET["settings"])) {
		include "actions/pagesettings.php";
		die();
	}
	include "actions/pageedit.php";
	die();
}

// settings page requested
if (isset($_GET["settings"])) {
	include "actions/settings.php";
	die();
}

// add page requested
if (isset($_GET["addpage"])) {
	include "actions/pageadd.php";
	die();
}

// manage pages page requested
if (isset($_GET["pages"])) {
	include "actions/pages.php";
	die();
}





// if nothing else is requested, load the homepage
CMS\Pages::setCurrentPage(CMS\Page::getHomepage());
include CMS\Theme::getThemeDirectory() . "/index.php";
die;