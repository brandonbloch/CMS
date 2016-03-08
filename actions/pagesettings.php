<?php

try {
	$page = CMS\Page::withID($_GET["edit"]);
	CMS\Pages::setCurrentPage($page);
} catch (Exception $e) {
	CMS\Browser::redirect(CMS\Site::getBaseURL() . "?pages");
}

$data = [
	"parent_page" => $page->getParentID(),
	"page_title" => $page->getTitle(),
	"page_shortname" => $page->getShortname(),
	"page_visibility" => $page->getVisibility(),
	"page_type" => $page->getPageTypeIdentifier(),
];

$errors = new CMS\Library\MessageList();

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
			$errors->addMessage( "Give the page a title.", CMS\Library\MessageList::WARNING );
		} else if ( ! CMS\Library\Validate::plainText( $data["page_title"] ) ) {
			$continue = false;
			$errors->addMessage( "Enter a valid page title.", CMS\Library\MessageList::WARNING );
		} else {
			$page->setTitle( $data["page_title"] );
		}
	}

	if ($page->getShortname() != $_POST["page_shortname"]) {
		$data["page_shortname"] = trim( $_POST["page_shortname"] );
		if ( ! $data["page_shortname"] ) {
			$continue = false;
			$errors->addMessage( "Give the page a shortname.", CMS\Library\MessageList::WARNING );
		} else if ( ! CMS\Library\Validate::plainText( $data["page_shortname"] ) ) {
			$continue = false;
			$errors->addMessage( "Enter a valid shortname.", CMS\Library\MessageList::WARNING );
		} else {
			$page->setShortname( $data["page_shortname"] );
			$slug = CMS\Library\Format::slug($data["page_shortname"]);
			if ($slug !== $page->getSlug()) {
				if (CMS\Pages::slugExists($slug)) {
					$continue = false;
					$errors->addMessage("A page with that shortname already exists.", CMS\Library\MessageList::WARNING);
				} else {
					$page->setSlug($slug);
				}
			}
		}
	}

	if (isset($_POST["page_visibility"])) {
		if (array_key_exists($_POST["page_visibility"], CMS\Page::getVisibilityDescriptions())) {
			$data["page_visibility"] = (int) $_POST["page_visibility"];
			$page->setVisibility($data["page_visibility"]);
		} else {
			$continue = false;
			$errors->addMessage("Select a valid page visibility option.", CMS\Library\MessageList::WARNING);
		}
	}

	if (CMS\Pages::pageTypeExists($_POST["page_type"])) {
		$data["page_type"] = $_POST["page_type"];
		$page->setPageType($data["page_type"]);
	} else {
		$continue = false;
		$errors->addMessage("Select an existing page type.", CMS\Library\MessageList::WARNING);
	}

	if ($continue) {
		$page->save();
		CMS\Browser::redirect($page->getURL());
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

			<?php if ($page->isHomepage()) { ?>
				<label>Visibility</label>
				<p class="input-replacement">The homepage needs to be public.</p>
			<?php } else { ?>
				<label for="page_visibility">Visibility</label>
				<select name="page_visibility" id="page_visibility">
					<option value="<?php echo CMS\Page::VISIBILITY_PUBLIC; ?>" <?php if ($data["page_visibility"] == CMS\Page::VISIBILITY_PUBLIC) echo "selected"; ?>>Public</option>
					<option value="<?php echo CMS\Page::VISIBILITY_PRIVATE; ?>" <?php if ($data["page_visibility"] == CMS\Page::VISIBILITY_PRIVATE) echo "selected"; ?>>Private</option>
					<option value="<?php echo CMS\Page::VISIBILITY_SECRET; ?>" <?php if ($data["page_visibility"] == CMS\Page::VISIBILITY_SECRET) echo "selected"; ?>>Secret</option>
				</select>

				<p id="visibility_explanation"><?php echo CMS\Page::getVisibilityDescriptions()[$data["page_visibility"]]; ?></p>
			<?php } ?>

		</section>

		<?php if (count(CMS\Pages::getPageTypes()) > 1) { ?>

		<section>

			<h2>Page Type</h2>

			<div class="radio-group cms-page-type-list">

				<?php foreach (CMS\Pages::getPageTypes() as $identifier => $type) { ?>
					<div class="page-type">
						<label>
							<input type="radio" name="page_type" id="page_type_<?php echo $identifier; ?>" value="<?php echo $identifier; ?>" <?php if ($data["page_type"] == $identifier) echo "checked"; ?>>
							<span class="page-type-preview" <?php if (isset($type["icon"])) echo "style=\"background-image: url('" . $type["icon"] . "')\""; ?>></span>
							<span class="page-type-name"><?php echo $type["name"]; ?></span>
							<?php if (isset($type["description"]) && trim($type["description"]) !== "") { ?>
								<span class="page-type-description"><?php echo $type["description"]; ?></span>
							<?php } ?>
						</label>
					</div>
				<?php } ?>

			</div>

		</section>

		<?php } ?>

		<input type="hidden" name="page_settings_submit">
		<button type="submit"><i class="fa fa-check-circle"></i> <?php echo \CMS\Localization::getLocalizedString(\CMS\Localization::BUTTON_SAVE); ?></button>

		<?php if (!$page->isHomepage()) { ?>
			<a href="<?php echo CMS\Site::getBaseURL(); ?>?delete=<?php echo $page->getID(); ?>" class="cancel-button" style="float: right; line-height: 2.2em;"><i class="fa fa-<?php echo \CMS\Localization::getLocalizedString(\CMS\Localization::ICON_DELETE_PAGE); ?>"></i><?php echo \CMS\Localization::getLocalizedString(\CMS\Localization::BUTTON_DELETE); ?></a>
		<?php } ?>

	</form>

</div>

<?php include "library/js/slugpreview-js.php"; ?>
<?php include "library/js/visibilitypreview-js.php"; ?>
<?php CMS\Core::includeCore(); ?>
</body>
</html>