<?php if (CMS\Theme::getColorScheme() == 1) { ?>
	<style>
		.admin-bar {
			background-color: #fff;
			border-color: #ccc;
		}
		.admin-bar a:hover,
		.admin-bar a:focus {
			color: #333;
		}
		body.cms-page-add .admin-bar .admin-bar-button-page-add,
		body.cms-pages .admin-bar .admin-bar-button-pages,
		body.cms-settings .admin-bar .admin-bar-button-settings,
		body.cms-page-edit .admin-bar .admin-bar-button-page-edit {
			background-color: #ccc;
			color: #333;
		}
		body.cms-page-add .admin-bar .admin-bar-button-page-add:hover,
		body.cms-page-add .admin-bar .admin-bar-button-page-add:focus,
		body.cms-pages .admin-bar .admin-bar-button-pages:hover,
		body.cms-pages .admin-bar .admin-bar-button-pages:focus,
		body.cms-settings .admin-bar .admin-bar-button-settings:hover,
		body.cms-settings .admin-bar .admin-bar-button-settings:focus,
		body.cms-page-edit .admin-bar .admin-bar-button-page-edit:hover,
		body.cms-page-edit .admin-bar .admin-bar-button-page-edit:focus {
			color: #333;
		}
	</style>
<?php } ?>

<div class="admin-bar">
	<div class="admin-bar-group admin-bar-group-left">
		<a class="admin-bar-button-home" href="<?php echo CMS\Site::getBaseURL(); ?>/" data-title="<?php echo LABEL_HOME; ?>"><span class="admin-bar-button-label"><?php echo LABEL_HOME; ?></span><i class="fa fa-<?php echo ICON_HOME; ?>"></i></a>
		<a class="admin-bar-button-pages" href="<?php echo CMS\Site::getBaseURL(); ?>/?pages" data-title="<?php echo LABEL_MANAGE_PAGES; ?>"><span class="admin-bar-button-label"><?php echo LABEL_MANAGE_PAGES; ?></span><i class="fa fa-<?php echo ICON_MANAGE_PAGES; ?>"></i></a>
		<a class="admin-bar-button-page-add" href="<?php echo CMS\Site::getBaseURL(); ?>/?addpage" data-title="<?php echo LABEL_ADD_PAGE; ?>"><span class="admin-bar-button-label"><?php echo LABEL_ADD_PAGE; ?></span><i class="fa fa-<?php echo ICON_ADD_PAGE; ?>"></i></a>
		<?php if (CMS\Pages::getCurrentPage() !== NULL) { ?>
			<a class="admin-bar-button-page-edit" href="<?php echo CMS\Site::getBaseURL(); ?>/?edit=<?php echo CMS\Pages::getCurrentPage()->getID(); ?>" data-title="<?php echo LABEL_EDIT_PAGE; ?>"><span class="admin-bar-button-label"><?php echo LABEL_EDIT_PAGE; ?></span><i class="fa fa-<?php echo ICON_EDIT_PAGE; ?>"></i></a>
		<?php } ?>
	</div>
	<div class="admin-bar-group admin-bar-group-right">
		<a class="admin-bar-button-settings" href="<?php echo CMS\Site::getBaseURL(); ?>/?settings" data-title="<?php echo LABEL_SETTINGS; ?>"><span class="admin-bar-button-label"><?php echo LABEL_SETTINGS; ?></span><i class="fa fa-<?php echo ICON_SETTINGS; ?>"></i></a>
		<a class="admin-bar-button-logout" href="<?php echo CMS\Site::getBaseURL(); ?>/?logout" data-title="<?php echo LABEL_LOGOUT; ?>"><span class="admin-bar-button-label"><?php echo LABEL_LOGOUT; ?></span><i class="fa fa-<?php echo ICON_LOGOUT; ?>"></i></a>
	</div>
</div>
