<?php

try {
	$page = Page::withID( $_GET["delete"] );
	Pages::setCurrentPage($page);
} catch (Exception $e) {
	Auth::redirect(Site::getBaseURL() . "?pages");
}

if ($page->isHomepage()) {
	Auth::redirect(Site::getBaseURL() . "?pages");
}

$data = array(
	"parent_page" => 0,
	"page_title" => "",
	"page_shortname" => "",
	"page_slug" => "",
);

$errors = new MessageCollector();

if (isset($_POST["page_delete_submit"])) {

	if ($_POST["page_has_children"]) {

		// make sure an option is selected

		if (isset($_POST["sub_page_decision"])) {
			if ($_POST["sub_page_decision"] == 1) {     // delete all sub-pages

				foreach ($page->getDescendants() as $descendant) {
					$descendant->delete();
				}

				$success = $page->delete();
				if ($success) {
					Auth::redirect(Site::getBaseURL() . "?pages");
				} else {
					$errors->addMessage("An error occurred trying to delete the page.", MessageCollector::DANGER);
				}

			} else if ($_POST["sub_page_decision"] == 2) {      // promote all sub-pages, by moving children to the level of this page

				foreach ($page->getChildren() as $child) {
					$child->setParentID($page->getParentID());
					$child->save();
				}
				$success = $page->delete();
				if ($success) {
					Auth::redirect(Site::getBaseURL() . "?pages");
				} else {
					$errors->addMessage("An error occurred trying to delete the page.", MessageCollector::DANGER);
				}

			}
		} else {
			$errors->addMessage("You must decide what to do with the sub-pages.", MessageCollector::WARNING);
		}

	} else {
		$success = $page->delete();
		if ($success) {

			if (isset($_GET["redirect"]) && $_GET["redirect"] == "home") {
				Auth::redirect(Site::getBaseURL());
			} else {
				Auth::redirect(Site::getBaseURL() . "?pages");
			}

		} else {
			$errors->addMessage("An error occurred trying to delete the page.", MessageCollector::DANGER);
		}

	}

}

?>

<html>
<head>
	<meta charset="utf-8">
	<title><?php echo LABEL_DELETE_PAGE; ?> - <?php echo Site::getTitle() ?></title>
	<link rel="stylesheet" href="<?php echo Theme::getStylesheetLink() ?>">
</head>
<body class="<?php echo Theme::getBodyClasses(); ?> cms-admin-page cms-page-delete">

<div class="cms-admin-page-interior">

	<h1><i class="fa fa-<?php echo ICON_DELETE_PAGE; ?>"></i><?php echo LABEL_DELETE_PAGE; ?></h1>

	<form class="cms-form no-label-form" action="" method="post">

		<?php echo $errors; ?>

		<section>

		<?php $children = $page->getChildrenIDs();
		if (count($children) > 0) { ?>

			<input type="hidden" name="page_has_children" value="1">

			<p><strong><?php echo $page->getTitle(); ?></strong> has sub-pages:</p>
			<ul>
				<?php foreach ($page->getChildren() as $child) { ?>
					<li><?php echo $child->getTitle(); ?></li>
				<?php } ?>
			</ul>

			<p>What would you like to do with them?</p>

			<div class="radio-group">

				<input type="radio" name="sub_page_decision" id="sub_page_delete" value="1">
				<label for="sub_page_delete">Delete all sub-pages along with this page</label>

				<input type="radio" name="sub_page_decision" id="sub_page_promote" value="2">
				<label for="sub_page_promote">Promote all sub-pages up one level</label>

			</div>

		<?php } else { ?>

			<input type="hidden" name="page_has_children" value="0">

			<p>Are you sure you want to delete <strong><?php echo $page->getTitle(); ?></strong>?</p>
			<p>This action cannot be undone.</p>

		<?php } ?>

		</section>

		<input type="hidden" name="page_delete_submit">
		<button type="submit" class="delete-button"><i class="fa fa-<?php echo ICON_DELETE_PAGE; ?>"></i> Delete</button>

		<a href="<?php echo Site::getBaseURL(); ?>?pages" class="cancel-button">cancel</a>

	</form>

</div>

<?php Core::includeCore(); ?>
</body>
</html>