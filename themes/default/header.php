<html class="<?php echo Theme::getHTMLClasses() ?>">
<head>
	<meta charset="utf-8">
	<meta name="description" content="<?php echo Site::getDescription() ?>">
	<?php if (!Pages::getCurrentPage() || Pages::getCurrentPage()->isHomepage()) { ?>
		<title><?php echo Site::getTitle() ?></title>
	<?php } else { ?>
		<title><?php echo Pages::getCurrentPage()->getTitle() . " - " . Site::getTitle() ?></title>
	<?php } ?>
	<link rel="stylesheet" href="<?php echo Theme::getStylesheetLink() ?>">
</head>
<body class="<?php echo Theme::getBodyClasses() ?>">

<header>
	<?php echo Theme::getNavigationMenu(); ?>
	<h1><?php echo Site::getTitle() ?></h1>
</header>