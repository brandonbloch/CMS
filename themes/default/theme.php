<?php

use \CMS\Pages as Pages;

Pages::registerPageType("right_sidebar", "Right Sidebar", [
	"description" => "A blank page with two editable zones.",
	"zones" => 2,
	"template" => "page-right-sidebar.php",
	"icon" => "page-right-sidebar.svg",
]);

Pages::registerPageType("left_sidebar", "Left Sidebar", [
	"description" => "A blank page with two editable zones.",
	"zones" => 2,
	"template" => "page-left-sidebar.php",
	"icon" => "page-left-sidebar.svg",
]);