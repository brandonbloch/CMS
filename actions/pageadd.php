<?php

$data = array(
	"parent_page" => 0,
	"page_title" => "",
	"page_shortname" => "",
);

$pageType = 0;      // TODO do something with this

$errors = new CMS\Library\MessageCollector();

if (isset($_POST["page_add_submit"])) {

	$continue = true;

	$data["parent_page"] = (int) $_POST["parent_page"];

	$data["page_title"] = trim($_POST["page_title"]);
	if ( ! $data["page_title"] ) {
		$continue = false;
		$errors->addMessage("Give the page a title.", CMS\Library\MessageCollector::WARNING);
	} else if (!CMS\Library\Validate::plainText($data["page_title"])) {
		$continue = false;
		$errors->addMessage("Enter a valid page title.", CMS\Library\MessageCollector::WARNING);
	}

	$data["page_shortname"] = trim($_POST["page_shortname"]);
	if (!$data["page_shortname"]) {
		$continue = false;
		$errors->addMessage("Give the page a shortname.", CMS\Library\MessageCollector::WARNING);
	} else if (!CMS\Library\Validate::plainText($data["page_shortname"])) {
		$continue = false;
		$errors->addMessage("Enter a valid shortname.", CMS\Library\MessageCollector::WARNING);
	} else {
		$slug = CMS\Library\Format::slug($data["page_shortname"]);
		if (CMS\Pages::slugExists($slug)) {
			$continue = false;
			$errors->addMessage("A page with that shortname already exists.", CMS\Library\MessageCollector::WARNING);
		}
	}

	if ($continue) {
		$page = CMS\Page::create($data["parent_page"], $data["page_title"], $data["page_shortname"], $slug);
		CMS\Auth::redirect($page->getURL());
	}

}

?><!DOCTYPE html>
<html lang="<?php echo \CMS\Localization::getLanguageCode(); ?>">
<head>
	<meta charset="utf-8">
	<title><?php echo \CMS\Localization::getLocalizedString(\CMS\Localization::LABEL_ADD_PAGE); ?> - <?php echo CMS\Site::getTitle() ?></title>
	<link rel="stylesheet" href="<?php echo CMS\Theme::getStylesheetLink() ?>">
</head>
<body class="<?php echo CMS\Theme::getBodyClasses(); ?> cms-admin-page cms-page-add">

<div class="cms-admin-page-interior">

	<h1><i class="fa fa-<?php echo \CMS\Localization::getLocalizedString(\CMS\Localization::ICON_ADD_PAGE); ?>"></i><?php echo \CMS\Localization::getLocalizedString(\CMS\Localization::LABEL_ADD_PAGE); ?></h1>

	<form class="cms-form" action="" method="post">

		<?php echo $errors; ?>

		<section>

			<h2>Page Settings</h2>

			<label for="parent_page">Parent Page</label>
			<select name="parent_page" id="parent_page">
				<option value="0" <?php if ($data["parent_page"] == 0) echo "selected"; ?>>(none)</option>
				<?php echo CMS\Pages::getPageHierarchySelectList($data["parent_page"]); ?>
			</select>

			<label for="page_title">Page Title</label>
			<input type="text" name="page_title" id="page_title" value="<?php echo $data["page_title"]; ?>">

			<label for="page_shortname">Page Shortname</label>
			<input type="text" name="page_shortname" id="page_shortname" value="<?php echo $data["page_shortname"]; ?>">

			<p id="shortname_explanation" style="display: <?php echo ($data["page_shortname"] == "") ? "none" : "block"; ?>;">The page will be displayed as <span id="page_nav_display"><?php echo $data["page_shortname"]; ?></span> in navigation menus and located at <span id="page_url_display"><?php echo CMS\Site::getBaseURL() . "/" . CMS\Library\Format::slug($data["page_shortname"]); ?></span></p>

		</section>

		<?php if (count(CMS\Pages::getPageTypes()) > 1) { ?>

		<section>

			<h2>Page Type</h2>

			<div class="radio-group cms-page-type-list">

				<?php foreach (CMS\Pages::getPageTypes() as $id => $type) { ?>
					<div class="page-type">
						<label>
							<input type="radio" name="page_type" id="page_type_<?php echo $id; ?>" value="<?php echo $id; ?>" <?php if ($pageType === $id) echo "checked"; ?>>
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

		<input type="hidden" name="page_add_submit">
		<button type="submit"><i class="fa fa-check-circle"></i> <?php echo \CMS\Localization::getLocalizedString(\CMS\Localization::BUTTON_CREATE); ?></button>

	</form>

</div>

<?php include "library/js/slugpreview-js.php"; ?>
<?php CMS\Core::includeCore(); ?>
</body>
</html>