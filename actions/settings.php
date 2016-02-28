<?php

$data = array(
	"site_title" => CMS\Site::getTitle(),
	"site_description" => CMS\Site::getDescription(),
	"admin_name" => CMS\Site::getAdminName(),
	"admin_email" => CMS\Site::getAdminEmail(),
);

$errors = new CMS\Library\MessageCollector();

if (isset($_POST["settings_submit"])) {

	$changes = false;

	if ($_POST["site_title"] !== $data["site_title"]) {
		$data["site_title"] = $_POST["site_title"];
		try {
			CMS\Site::setTitle($data["site_title"]);
			$changes = true;
		} catch (InvalidArgumentException $e) {
			$errors->addMessage("The site title you entered is invalid.", CMS\Library\MessageCollector::WARNING);
		}
	}

	if ($_POST["site_description"] !== $data["site_description"]) {
		$data["site_description"] = $_POST["site_description"];
		try {
			CMS\Site::setDescription($data["site_description"]);
			$changes = true;
		} catch (InvalidArgumentException $e) {
			$errors->addMessage("The site description you entered is invalid.", CMS\Library\MessageCollector::WARNING);
		}
	}

	if ($_POST["active_theme"] !== CMS\Theme::getActiveTheme()) {
		CMS\Theme::setActiveTheme($_POST["active_theme"]);
		$changes = true;
	}

	if ($_POST["color_scheme"] !== CMS\Theme::getColorScheme()) {
		CMS\Theme::setColorScheme($_POST["color_scheme"]);
		$changes = true;
	}

	if ($_POST["admin_name"] !== $data["admin_name"]) {
		$data["admin_name"] = $_POST["admin_name"];
		try {
			CMS\Site::setAdminName($data["admin_name"]);
			$changes = true;
		} catch (InvalidArgumentException $e) {
			$errors->addMessage("The administrator name you entered is invalid.", CMS\Library\MessageCollector::WARNING);
		}
	}

	if ($_POST["admin_email"] !== $data["admin_email"]) {
		$data["admin_email"] = $_POST["admin_email"];
		try {
			CMS\Site::setAdminEmail($data["admin_email"]);
			$changes = true;
		} catch (InvalidArgumentException $e) {
			$errors->addMessage("The administrator email you entered is invalid.", CMS\Library\MessageCollector::WARNING);
		}
	}

	if (!$changes) {
		$errors->addMessage("There are no changes to save.", \CMS\Library\MessageCollector::INFO);
	}

}

?><!DOCTYPE html>
<html lang="<?php echo \CMS\Localization::getLanguageCode(); ?>">
<head>
	<meta charset="utf-8">
	<title><?php echo \CMS\Localization::getLocalizedString(\CMS\Localization::LABEL_SETTINGS); ?> - <?php echo CMS\Site::getTitle() ?></title>
	<link rel="stylesheet" href="<?php echo CMS\Theme::getStylesheetLink() ?>">
</head>
<body class="<?php echo CMS\Theme::getBodyClasses(); ?> cms-admin-page cms-settings">

<div class="cms-admin-page-interior">

	<h1><i class="fa fa-<?php echo \CMS\Localization::getLocalizedString(\CMS\Localization::ICON_SETTINGS); ?>"></i><?php echo \CMS\Localization::getLocalizedString(\CMS\Localization::LABEL_SETTINGS); ?></h1>

	<form class="cms-form" action="" method="post">

		<?php echo $errors; ?>

		<section>

			<h2>Site Settings</h2>

			<label for="site_title">Site Title</label>
			<input type="text" name="site_title" id="site_title" value="<?php echo $data["site_title"]; ?>" placeholder="<?php echo CMS\Site::getTitle(); ?>">

			<label for="site_description">Site Description</label>
			<input type="text" name="site_description" id="site_description" value="<?php echo $data["site_description"]; ?>" placeholder="<?php echo CMS\Site::getDescription(); ?>">

		</section>

		<section>

			<h2>Theme Settings</h2>

			<label for="active_theme">Active Theme</label>
			<div class="cms-themes-list">
				<?php $themes = preg_grep('/^([^.])/', scandir("./themes"));
				$activeTheme = CMS\Theme::getActiveTheme();
				foreach ($themes as $themeName) {
					// they need to at least have a main template file for displaying pages
					if (file_exists("./themes/" . $themeName . '/index.php')) {
						// if the theme's settings file cannot be read, it cannot be chosen
						try {
							$themeSettings = CMS\Theme::readThemeFile($themeName); ?>
							<div class="theme-thumbnail">
								<label>
									<input type="radio" name="active_theme" id="theme_option_<?php echo $themeName; ?>" value="<?php echo $themeName; ?>" <?php if ($activeTheme === $themeName) echo "checked"; ?>>
									<span class="theme-preview" <?php if (isset($themeSettings["thumbnail"])) echo "style=\"background-image: url('" . CMS\Site::getBaseURL() . "/themes/" . $themeName . "/" . $themeSettings["thumbnail"] . "')\""; ?>></span>
									<span class="theme-name"><?php echo $themeSettings["name"]; ?></span>
									<?php if (isset($themeSettings["description"]) && trim($themeSettings["description"]) !== "") { ?>
										<span class="theme-description"><?php echo $themeSettings["description"]; ?></span>
									<?php } ?>
								</label>
							</div>
						<?php } catch ( RuntimeException $e ) {
						}
					}
				} ?>
			</div>

			<label for="">Toolbar Color</label>
			<div class="radio-group">

				<?php foreach (CMS\Theme::getColorSchemeList() as $code => $colorScheme) { ?>
					<div class="radio-group-item">
						<input type="radio" name="color_scheme" id="color_scheme_<?php echo $code; ?>" value="<?php echo $code; ?>" <?php if (CMS\Theme::getColorScheme() == $code) echo "checked"; ?>>
						<label for="color_scheme_<?php echo $code; ?>"><?php echo $colorScheme; ?></label>
					</div>
				<?php } ?>

			</div>

		</section>

		<section>

			<h2>Administrator Settings</h2>

			<label for="admin_name">Name</label>
			<input type="text" name="admin_name" id="admin_name" value="<?php echo $data["admin_name"]; ?>" placeholder="<?php echo CMS\Site::getAdminName(); ?>">

			<label for="admin_email">Email Address</label>
			<input type="email" name="admin_email" id="admin_email" value="<?php echo $data["admin_email"]; ?>" placeholder="<?php echo CMS\Site::getAdminEmail(); ?>">

			<label>Username</label>
			<p class="input-replacement">not implemented yet &nbsp; <a title="Change Username" class="disabled"><i class="fa fa-pencil"></i></a></p>

			<label>Password</label>
			<p class="input-replacement">not implemented yet &nbsp; <a title="Change Password" class="disabled"><i class="fa fa-pencil"></i></a></p>

		</section>

		<input type="hidden" name="settings_submit">
		<button type="submit"><i class="fa fa-check-circle"></i> <?php echo \CMS\Localization::getLocalizedString(\CMS\Localization::BUTTON_SAVE); ?></button>

	</form>

</div>

<?php CMS\Core::includeCore(); ?>
</body>
</html>