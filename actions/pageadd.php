<?php

$data = array(
	"parent_page" => 0,
	"page_title" => "",
	"page_shortname" => "",
);

$pageType = 0;      // TODO do something with this

$errors = new MessageCollector();

if (isset($_POST["page_add_submit"])) {

	$continue = true;

	$data["parent_page"] = (int) $_POST["parent_page"];

	$data["page_title"] = trim($_POST["page_title"]);
	if ( ! $data["page_title"] ) {
		$continue = false;
		$errors->addMessage( "Give the page a title.", MessageCollector::WARNING );
	} else if ( ! Validate::plainText( $data["page_title"] ) ) {
		$continue = false;
		$errors->addMessage( "Enter a valid page title.", MessageCollector::WARNING );
	}

	$data["page_shortname"] = trim($_POST["page_shortname"]);
	if ( ! $data["page_shortname"] ) {
		$continue = false;
		$errors->addMessage( "Give the page a shortname.", MessageCollector::WARNING );
	} else if ( ! Validate::plainText( $data["page_shortname"] ) ) {
		$continue = false;
		$errors->addMessage( "Enter a valid shortname.", MessageCollector::WARNING );
	} else {
		$slug = Format::slug($data["page_shortname"]);
		if (Pages::slugExists($slug)) {
			$continue = false;
			$errors->addMessage("A page with that shortname already exists.", MessageCollector::WARNING);
		}
	}

	if ($continue) {
		$page = Page::create($data["parent_page"], $data["page_title"], $data["page_shortname"], $slug);
		Auth::redirect($page->getURL());
	}

}

?>

<html>
<head>
	<meta charset="utf-8">
	<title><?php echo LABEL_ADD_PAGE; ?> - <?php echo Site::getTitle() ?></title>
	<link rel="stylesheet" href="<?php echo Theme::getStylesheetLink() ?>">
</head>
<body class="<?php echo Theme::getBodyClasses(); ?> cms-admin-page cms-page-add">

<div class="cms-admin-page-interior">

	<h1><i class="fa fa-<?php echo ICON_ADD_PAGE; ?>"></i><?php echo LABEL_ADD_PAGE; ?></h1>

	<form class="cms-form" action="" method="post">

		<?php echo $errors; ?>

		<section>

			<h2>Page Settings</h2>

			<label for="parent_page">Parent Page</label>
			<select name="parent_page" id="parent_page">
				<option value="0" <?php if ($data["parent_page"] == 0) echo "selected"; ?>>(none)</option>
				<?php echo Pages::getPageHierarchySelectList($data["parent_page"]); ?>
			</select>

			<label for="page_title">Page Title</label>
			<input type="text" name="page_title" id="page_title" value="<?php echo $data["page_title"]; ?>">

			<label for="page_shortname">Page Shortname</label>
			<input type="text" name="page_shortname" id="page_shortname" value="<?php echo $data["page_shortname"]; ?>">

			<p id="shortname_explanation" style="display: <?php echo ($data["page_shortname"] == "") ? "none" : "block"; ?>;">The page will be displayed as <span id="page_nav_display"><?php echo $data["page_shortname"]; ?></span> in navigation menus and located at <span id="page_url_display"><?php echo Site::getBaseURL() . "/" . Format::slug($data["page_shortname"]); ?></span></p>

		</section>

		<?php if (count(Pages::getPageTypes()) > 1) { ?>

		<section>

			<h2>Page Type</h2>

			<div class="radio-group cms-page-type-list">

				<?php foreach (Pages::getPageTypes() as $id => $type) { ?>
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
		<button type="submit"><i class="fa fa-check-circle"></i> Create</button>

	</form>

</div>

<?php include "library/js/slugpreview-js.php"; ?>
<?php Core::includeCore(); ?>
</body>
</html>