<?php

$data = array(
	"site_title" => Site::getTitle(),
	"site_description" => Site::getDescription(),
	"admin_name" => Site::getAdminName(),
	"admin_email" => Site::getAdminEmail(),
);

$errors = new MessageCollector();

if (isset($_POST["settings_submit"])) {

	if ($_POST["site_title"] !== $data["site_title"]) {
		$data["site_title"] = $_POST["site_title"];
		try {
			Site::setTitle($data["site_title"]);
		} catch (InvalidArgumentException $e) {
			$errors->addMessage("The site title you entered is invalid.", MessageCollector::WARNING);
		}
	}

	if ($_POST["site_description"] !== $data["site_description"]) {
		$data["site_description"] = $_POST["site_description"];
		try {
			Site::setDescription($data["site_description"]);
		} catch (InvalidArgumentException $e) {
			$errors->addMessage("The site description you entered is invalid.", MessageCollector::WARNING);
		}
	}

	if ($_POST["active_theme"] !== Theme::getActiveTheme()) {
		Theme::setActiveTheme($_POST["active_theme"]);
	}

	if ($_POST["color_scheme"] !== Theme::getColorScheme()) {
		Theme::setColorScheme($_POST["color_scheme"]);
	}

	if ($_POST["admin_name"] !== $data["admin_name"]) {
		$data["admin_name"] = $_POST["admin_name"];
		try {
			Site::setAdminName($data["admin_name"]);
		} catch (InvalidArgumentException $e) {
			$errors->addMessage("The administrator name you entered is invalid.", MessageCollector::WARNING);
		}
	}

	if ($_POST["admin_email"] !== $data["admin_email"]) {
		$data["admin_email"] = $_POST["admin_email"];
		try {
			Site::setAdminEmail($data["admin_email"]);
		} catch (InvalidArgumentException $e) {
			$errors->addMessage("The administrator email you entered is invalid.", MessageCollector::WARNING);
		}
	}

}

?>

<html>
<head>
	<meta charset="utf-8">
	<title><?php echo LABEL_SETTINGS; ?> - <?php echo Site::getTitle() ?></title>
	<link rel="stylesheet" href="<?php echo Theme::getStylesheetLink() ?>">
</head>
<body class="<?php echo Theme::getBodyClasses(); ?> cms-admin-page cms-settings">

<div class="cms-admin-page-interior">

	<h1><i class="fa fa-<?php echo ICON_SETTINGS; ?>"></i><?php echo LABEL_SETTINGS; ?></h1>

	<form class="cms-form" action="" method="post">

		<?php echo $errors; ?>

		<section>

			<h2>Site Settings</h2>

			<label for="site_title">Site Title</label>
			<input type="text" name="site_title" id="site_title" value="<?php echo $data["site_title"]; ?>" placeholder="<?php echo Site::getTitle(); ?>">

			<label for="site_description">Site Description</label>
			<input type="text" name="site_description" id="site_description" value="<?php echo $data["site_description"]; ?>" placeholder="<?php echo Site::getDescription(); ?>">

		</section>

		<section>

			<h2>Theme Settings</h2>

			<label for="active_theme">Active Theme</label>
			<select name="active_theme" id="active_theme">
				<?php $themes = preg_grep('/^([^.])/', scandir("./themes"));
				$activeTheme = Theme::getActiveTheme();
				foreach ($themes as $themeName) {
					// they need to at least have a main template file for displaying pages
					if (file_exists("./themes/" . $themeName . '/index.php')) {
						// if the theme's settings file cannot be read, it cannot be chosen
						try {
							$themeSettings = Theme::readThemeFile( $themeName ); ?>
							<option value="<?php echo $themeName; ?>" <?php if ( $themeName == $activeTheme )
								echo 'selected="selected"' ?>><?php echo $themeSettings["name"]; ?></option>
						<?php } catch ( RuntimeException $e ) {
						}
					}
				} ?>
			</select>

			<label for="">Toolbar Color</label>
			<div class="radio-group">

				<?php foreach (Theme::getColorSchemeList() as $code => $colorScheme) { ?>
					<div class="radio-group-item">
						<input type="radio" name="color_scheme" id="color_scheme_<?php echo $code; ?>" value="<?php echo $code; ?>" <?php if (Theme::getColorScheme() == $code) echo "checked"; ?>>
						<label for="color_scheme_<?php echo $code; ?>"><?php echo $colorScheme; ?></label>
					</div>
				<?php } ?>

			</div>

		</section>

		<section>

			<h2>Administrator Settings</h2>

			<label for="admin_name">Name</label>
			<input type="text" name="admin_name" id="admin_name" value="<?php echo $data["admin_name"]; ?>" placeholder="<?php echo Site::getAdminName(); ?>">

			<label for="admin_email">Email Address</label>
			<input type="email" name="admin_email" id="admin_email" value="<?php echo $data["admin_email"]; ?>" placeholder="<?php echo Site::getAdminEmail(); ?>">

			<label>Username</label>
			<p class="input-replacement">not implemented yet &nbsp; <a title="Change Username" class="disabled"><i class="fa fa-pencil"></i></a></p>

			<label>Password</label>
			<p class="input-replacement">not implemented yet &nbsp; <a title="Change Password" class="disabled"><i class="fa fa-pencil"></i></a></p>

		</section>

		<input type="hidden" name="settings_submit">
		<button type="submit"><i class="fa fa-check-circle"></i> <?php echo BUTTON_SAVE; ?></button>

	</form>

</div>

<?php Core::includeCore(); ?>
</body>
</html>