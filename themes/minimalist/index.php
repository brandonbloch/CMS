<?php $page = CMS\Pages::getCurrentPage(); ?>

<html>
<head>
	<title><?php echo CMS\Site::getTitle(); ?></title>
</head>
<body>

<?php echo CMS\Markdown::parse($page->getContent());

CMS\Core::includeCore(); ?>

</body>
</html>