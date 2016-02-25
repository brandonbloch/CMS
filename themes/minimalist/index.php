<?php $page = Pages::getCurrentPage(); ?>

<html>
<head>
	<title><?php echo Site::getTitle(); ?></title>
</head>
<body>

<?php echo Markdown::parse($page->getContent());

Core::includeCore(); ?>

</body>
</html>