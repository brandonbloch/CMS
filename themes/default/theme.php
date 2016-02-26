<?php

CMS\Pages::registerPageType("Right Sidebar", array(
	"description" => "A blank page with two editable zones.",
	"zones" => 2,
	"template" => "page-right-sidebar.php",
	"icon" => "page-right-sidebar.svg",
));

CMS\Pages::registerPageType("Left Sidebar", array(
	"description" => "A blank page with two editable zones.",
	"zones" => 2,
	"template" => "page-left-sidebar.php",
	"icon" => "page-left-sidebar.svg",
));