<?php

try {
	$page = CMS\Page::withID($_GET["edit"]);
	CMS\Pages::setCurrentPage($page);
} catch (Exception $e) {
	CMS\Auth::redirect(CMS\Site::getBaseURL() . "?pages");
}

$data = array(
	"parent_page" => $page->getParentID(),
	"page_title" => $page->getTitle(),
	"page_shortname" => $page->getShortname(),
);

$errors = new CMS\Library\MessageCollector();

if (isset($_POST["page_settings_submit"])) {

	$continue = true;

	if (isset($_POST["parent_page"])) {
		$data["parent_page"] = (int) $_POST["parent_page"];
		$page->setParentID($_POST["parent_page"]);
	}

	if ($page->getTitle() != $_POST["page_title"]) {
		$data["page_title"] = trim( $_POST["page_title"] );
		if ( ! $data["page_title"] ) {
			$continue = false;
			$errors->addMessage( "Give the page a title.", CMS\Library\MessageCollector::WARNING );
		} else if ( ! CMS\Library\Validate::plainText( $data["page_title"] ) ) {
			$continue = false;
			$errors->addMessage( "Enter a valid page title.", CMS\Library\MessageCollector::WARNING );
		} else {
			$page->setTitle( $data["page_title"] );
		}
	}

	if ($page->getShortname() != $_POST["page_shortname"]) {
		$data["page_shortname"] = trim( $_POST["page_shortname"] );
		if ( ! $data["page_shortname"] ) {
			$continue = false;
			$errors->addMessage( "Give the page a shortname.", CMS\Library\MessageCollector::WARNING );
		} else if ( ! CMS\Library\Validate::plainText( $data["page_shortname"] ) ) {
			$continue = false;
			$errors->addMessage( "Enter a valid shortname.", CMS\Library\MessageCollector::WARNING );
		} else {
			$page->setShortname( $data["page_shortname"] );
			$slug = CMS\Library\Format::slug($data["page_shortname"]);
			if ($slug !== $page->getSlug()) {
				if (CMS\Pages::slugExists($slug)) {
					$continue = false;
					$errors->addMessage("A page with that shortname already exists.", CMS\Library\MessageCollector::WARNING);
				} else {
					$page->setSlug($slug);
				}
			}
		}
	}

	if ($continue) {
		$page->save();
		CMS\Auth::redirect($page->getURL());
	}

}

?><!DOCTYPE html>
<html lang="<?php echo \CMS\Localization::getLanguageCode(); ?>">
<head>
	<meta charset="utf-8">
	<title><?php echo \CMS\Localization::getLocalizedString(\CMS\Localization::LABEL_PAGE_SETTINGS); ?> - <?php echo CMS\Site::getTitle() ?></title>
	<link rel="stylesheet" href="<?php echo CMS\Theme::getStylesheetLink() ?>">
</head>
<body class="<?php echo CMS\Theme::getBodyClasses(); ?> cms-admin-page cms-page-settings">

<div class="cms-admin-page-interior">

	<h1><i class="fa fa-<?php echo \CMS\Localization::getLocalizedString(\CMS\Localization::ICON_PAGE_SETTINGS); ?>"></i><?php echo \CMS\Localization::getLocalizedString(\CMS\Localization::LABEL_PAGE_SETTINGS); ?></h1>

	<form class="cms-form" action="" method="post">

		<?php echo $errors; ?>

		<section>

			<?php if ($page->isHomepage()) { ?>
				<label>Parent Page</label>
				<p class="input-replacement">The homepage needs to be a top-level page.</p>
			<?php } else { ?>
				<label for="parent_page">Parent Page</label>
				<select name="parent_page" id="parent_page">
					<option value="0" <?php if ($data["parent_page"] == 0) echo "selected"; ?>>(none)</option>
					<?php echo CMS\Pages::getPageHierarchySelectList($data["parent_page"], $page->getID()); ?>
				</select>
			<?php } ?>

			<label for="page_title">Page Title</label>
			<input type="text" name="page_title" id="page_title" value="<?php echo $data["page_title"]; ?>">

			<label for="page_shortname">Page Shortname</label>
			<input type="text" name="page_shortname" id="page_shortname" value="<?php echo $data["page_shortname"]; ?>">

			<p id="shortname_explanation" style="display: <?php echo ($data["page_shortname"] == "") ? "none" : "block"; ?>;">The page will be displayed as <span id="page_nav_display"><?php echo $data["page_shortname"]; ?></span> in navigation menus and located at <span id="page_url_display"><?php echo CMS\Site::getBaseURL() . "/" . CMS\Library\Format::slug($data["page_shortname"]); ?></span></p>

		</section>




		<input type="hidden" name="page_settings_submit">
		<button type="submit"><i class="fa fa-check-circle"></i> <?php echo \CMS\Localization::getLocalizedString(\CMS\Localization::BUTTON_SAVE); ?></button>

	</form>

</div>

<?php include "library/js/slugpreview-js.php"; ?>
<?php CMS\Core::includeCore(); ?>
</body>
</html>