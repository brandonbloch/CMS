<?php

try {
	$page = CMS\Page::withID($_GET["edit"]);
	CMS\Pages::setCurrentPage($page);
} catch (Exception $e) {
	CMS\Browser::redirect(CMS\Site::getBaseURL() . "?pages");
}

$data = [
	"parent_page" => $page->getParentID(),
	"page_title" => $page->getTitle(),
	"page_shortname" => $page->getShortname(),
];

if (isset($_POST["page_edit_submit"])) {
}

$templateFile = CMS\Theme::getTemplateForPageType($page->getPageTypeIdentifier());
include CMS\Theme::getThemeDirectory() . "/" . $templateFile;
