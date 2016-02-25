<html>
<head>
	<meta charset="utf-8">
	<title><?php echo LABEL_MANAGE_PAGES; ?> - <?php echo Site::getTitle() ?></title>
	<link rel="stylesheet" href="<?php echo Theme::getStylesheetLink() ?>">
</head>
<body class="<?php echo Theme::getBodyClasses(); ?> cms-admin-page cms-pages">

<div class="cms-admin-page-interior">

	<h1><a href="<?php echo Site::getBaseURL(); ?>/?addpage" title="<?php echo LABEL_ADD_PAGE; ?>" class="float-right"><i class="fa fa-<?php echo ICON_ADD_PAGE; ?>"></i></a><i class="fa fa-<?php echo ICON_MANAGE_PAGES; ?>"></i><?php echo LABEL_MANAGE_PAGES; ?></h1>

	<table class="cms-admin-table">
		<thead>
			<tr>
				<th>Title</th>
				<th>Shortname</th>
				<th class="actions-list"></th>
			</tr>
		</thead>
		<tbody>
		<?php echo Pages::getPageHierarchyTableList(); ?>
		</tbody>
	</table>

</div>

<?php Core::includeCore(); ?>
</body>
</html>