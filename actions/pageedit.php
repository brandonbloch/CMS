<?php

try {
	$page = Page::withID($_GET["edit"]);
	Pages::setCurrentPage($page);
} catch (Exception $e) {
	Auth::redirect(Site::getBaseURL() . "?pages");
}

$data = array(
	"parent_page" => $page->getParentID(),
	"page_title" => $page->getTitle(),
	"page_shortname" => $page->getShortname(),
);

$errors = new MessageCollector();

if (isset($_POST["page_edit_submit"])) {

	$continue = true;

	if (isset($_POST["parent_page"])) {
		$data["parent_page"] = (int) $_POST["parent_page"];
		$page->setParentID($_POST["parent_page"]);
	}

	if ($page->getTitle() != $_POST["page_title"]) {
		$data["page_title"] = trim( $_POST["page_title"] );
		if ( ! $data["page_title"] ) {
			$continue = false;
			$errors->addMessage( "Give the page a title.", MessageCollector::WARNING );
		} else if ( ! Validate::plainText( $data["page_title"] ) ) {
			$continue = false;
			$errors->addMessage( "Enter a valid page title.", MessageCollector::WARNING );
		} else {
			$page->setTitle( $data["page_title"] );
		}
	}

	if ($page->getShortname() != $_POST["page_shortname"]) {
		$data["page_shortname"] = trim( $_POST["page_shortname"] );
		if ( ! $data["page_shortname"] ) {
			$continue = false;
			$errors->addMessage( "Give the page a shortname.", MessageCollector::WARNING );
		} else if ( ! Validate::plainText( $data["page_shortname"] ) ) {
			$continue = false;
			$errors->addMessage( "Enter a valid shortname.", MessageCollector::WARNING );
		} else {
			$page->setShortname( $data["page_shortname"] );
			$slug = Format::slug($data["page_shortname"]);
			if ($slug !== $page->getSlug()) {
				if (Pages::slugExists($slug)) {
					$continue = false;
					$errors->addMessage("A page with that shortname already exists.", MessageCollector::WARNING);
				} else {
					$page->setSlug($slug);
				}
			}
		}
	}

	if ($continue) {
		$page->save();

		if (isset($_GET["redirect"]) && $_GET["redirect"] == "pages") {
			Auth::redirect(Site::getBaseURL() . "?pages");
		} else {
			Auth::redirect($page->getURL());
		}
	}

}

?>

<html>
<head>
	<meta charset="utf-8">
	<title><?php echo LABEL_EDIT_PAGE; ?> - <?php echo Site::getTitle() ?></title>
	<link rel="stylesheet" href="<?php echo Theme::getStylesheetLink() ?>">
</head>
<body class="<?php echo Theme::getBodyClasses(); ?> cms-admin-page cms-page-edit">

<div class="cms-admin-page-interior">

	<h1><i class="fa fa-<?php echo ICON_EDIT_PAGE; ?>"></i><?php echo LABEL_EDIT_PAGE; ?></h1>

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
					<?php echo Pages::getPageHierarchySelectList($data["parent_page"], $page->getID()); ?>
				</select>
			<?php } ?>

			<label for="page_title">Page Title</label>
			<input type="text" name="page_title" id="page_title" value="<?php echo $data["page_title"]; ?>">

			<label for="page_shortname">Page Shortname</label>
			<input type="text" name="page_shortname" id="page_shortname" value="<?php echo $data["page_shortname"]; ?>">

			<p id="shortname_explanation" style="display: <?php echo ($data["page_shortname"] == "") ? "none" : "block"; ?>;">The page will be displayed as <span id="page_nav_display"><?php echo $data["page_shortname"]; ?></span> in navigation menus and located at <span id="page_url_display"><?php echo Site::getBaseURL() . "/" . Format::slug($data["page_shortname"]); ?></span></p>

		</section>




		<input type="hidden" name="page_edit_submit">
		<button type="submit"><i class="fa fa-check-circle"></i> <?php echo BUTTON_SAVE; ?></button>

	</form>

</div>

<?php include "library/js/slugpreview-js.php"; ?>
<?php Core::includeCore(); ?>
</body>
</html>