<?php
use CMS\Core as Core;
use CMS\Pages as Pages;
use CMS\Site as Site;
use CMS\Library\Markdown as Markdown;
?>

<?php $page = Pages::getCurrentPage(); ?>

<html>
<head>
	<title><?php echo Site::getTitle(); ?></title>
</head>
<body>

<?php echo $page->getZoneOutput(0);

Core::includeCore(); ?>

</body>
</html>