<?php

// get the configuration options
require_once "configuration.php";
require_once "localization.php";

// include the library/php directory for class auto-loading
spl_autoload_register(function($class) {
	$class = str_replace("\\", "/", $class);
	include_once "library/php/$class.php";
});






// begin main pageloader action

if (file_exists(CMS\Theme::getThemeDirectory() . "/theme.php")) {
	include_once CMS\Theme::getThemeDirectory() . "/theme.php";
}

// 404 page was redirected to this page by .htaccess
if (isset($_GET["notfound"])) {
	CMS\Site::set404Response();
}

// logout process requested
if (isset($_GET["logout"])) {
	include "actions/logout.php";
	die();
}

// page requested by slug
if (isset($_GET["slug"])) {
	// if on the homepage, redirect Site::getBaseURL (this will run again and reach the bottom)

	// if the page slug is nonexistent, return a 404 response
	try {
		$currentPage = CMS\Page::withSlug($_GET["slug"]);
	} catch (Exception $e) {
		CMS\Site::set404Response();
	}

	// if on the homepage, redirect Site::getBaseURL (this will run again and reach the bottom)
	if ($currentPage->isHomepage()) {
		CMS\Auth::redirect(CMS\Site::getBaseURL());
	}

	CMS\Pages::setCurrentPage($currentPage);
	include CMS\Theme::getThemeDirectory() . "/index.php";
	die();
}

// page requested by ID
if (isset($_GET["id"])) {
	// if the page ID is invalid, return a 404 response
	try {
		$currentPage = CMS\Page::withID($_GET["id"]);
	} catch (Exception $e) {
		CMS\Site::set404Response();
	}

	// if on the homepage, redirect Site::getBaseURL (this will run again and reach the bottom)
	if ($currentPage->isHomepage()) {
		CMS\Auth::redirect(Site::getBaseURL());
	}

	CMS\Pages::setCurrentPage($currentPage);
	include CMS\Theme::getThemeDirectory() . "/index.php"; // TODO if the active theme has a page.php file, use that instead
	die();
}

// delete page requested by page ID
if (isset($_GET["delete"]) && CMS\Validate::int($_GET["delete"])) {
	include "actions/pagedelete.php";
	die();
}

// edit page requested by page ID
if (isset($_GET["edit"]) && CMS\Validate::int($_GET["edit"])) {
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