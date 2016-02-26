<html>
<head>
	<meta charset="utf-8">
	<title><?php echo LABEL_MANAGE_PAGES; ?> - <?php echo CMS\Site::getTitle() ?></title>
	<link rel="stylesheet" href="<?php echo CMS\Theme::getStylesheetLink() ?>">
</head>
<body class="<?php echo CMS\Theme::getBodyClasses(); ?> cms-admin-page cms-pages">

<div class="cms-admin-page-interior">

	<h1><a href="<?php echo CMS\Site::getBaseURL(); ?>/?addpage" title="<?php echo LABEL_ADD_PAGE; ?>" class="float-right"><i class="fa fa-<?php echo ICON_ADD_PAGE; ?>"></i></a><i class="fa fa-<?php echo ICON_MANAGE_PAGES; ?>"></i><?php echo LABEL_MANAGE_PAGES; ?></h1>

	<table class="cms-admin-table">
		<thead>
			<tr>
				<th>Title</th>
				<th>Shortname</th>
				<th class="actions-list"></th>
			</tr>
		</thead>
		<tbody>
		<?php echo CMS\Pages::getPageHierarchyTableList(); ?>
		</tbody>
	</table>

</div>

<?php CMS\Core::includeCore(); ?>
</body>
</html>