<!DOCTYPE html>
<html lang="<?php echo \CMS\Localization::getLanguageCode() ?>">
<head>
	<meta charset="utf-8">
	<meta name="description" content="<?php echo CMS\Site::getDescription() ?>">
	<?php if (!CMS\Pages::getCurrentPage()) { ?>
		<title>Not Found - <?php echo CMS\Site::getTitle() ?></title>
	<?php } else if (!CMS\Pages::getCurrentPage() || CMS\Pages::getCurrentPage()->isHomepage()) { ?>
		<title><?php echo CMS\Site::getTitle() ?></title>
	<?php } else { ?>
		<title><?php echo CMS\Pages::getCurrentPage()->getTitle() . " - " . CMS\Site::getTitle() ?></title>
	<?php } ?>
	<link rel="stylesheet" href="<?php echo CMS\Theme::getStylesheetLink() ?>">
</head>
<body class="<?php echo CMS\Theme::getBodyClasses() ?>">

<header>
	<?php echo CMS\Theme::getNavigationMenu() ?>
	<h1><a href="<?php echo CMS\Site::getBaseURL() ?>"><?php echo CMS\Site::getTitle() ?></a></h1>
</header>