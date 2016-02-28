<?php

use \CMS\Localization as Loc;
use \CMS\Pages as Pages;
use \CMS\Site as Site;
use \CMS\Theme as Theme;

?><!DOCTYPE html>
<html lang="<?php echo Loc::getLanguageCode() ?>">
<head>
	<meta charset="utf-8">
	<meta name="description" content="<?php echo Site::getDescription() ?>">
	<?php if (!Pages::getCurrentPage()) { ?>
		<title>Not Found - <?php echo Site::getTitle() ?></title>
	<?php } else if (!Pages::getCurrentPage() || Pages::getCurrentPage()->isHomepage()) { ?>
		<title><?php echo Site::getTitle() ?></title>
	<?php } else { ?>
		<title><?php echo Pages::getCurrentPage()->getTitle() . " - " . Site::getTitle() ?></title>
	<?php } ?>
	<link rel="stylesheet" href="<?php echo Theme::getStylesheetLink() ?>">
</head>
<body class="<?php echo Theme::getBodyClasses() ?>">

<header>
	<?php echo Theme::getNavigationMenu() ?>
	<h1><a href="<?php echo Site::getBaseURL() ?>"><?php echo Site::getTitle() ?></a></h1>
</header>