<html>
<head>
	<meta charset="utf-8">
	<title><?php echo \CMS\Localization::getLocalizedString(\CMS\Localization::LABEL_404); ?> - <?php echo Site::getTitle() ?></title>
	<link rel="stylesheet" href="<?php echo Theme::getStylesheetLink() ?>">
</head>
<body class="<?php echo Theme::getBodyClasses(); ?> cms-admin-page">

<div class="cms-admin-page-interior">

	<h1><?php echo \CMS\Localization::getLocalizedString(\CMS\Localization::LABEL_404); ?></h1>

	<p>The page you requested is not available.</p>

	<p>This could mean that the page has moved, or no longer exists.</p>

</div>

<?php Core::includeCore(); ?>
</body>
</html>