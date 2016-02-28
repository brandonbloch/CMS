<?php

use \CMS\Localization as Loc;
use \CMS\Theme as Theme;

Theme::includeHeader();

?>

	<h1><?php echo Loc::getLocalizedString(\CMS\Localization::LABEL_404); ?></h1>

	<p>The page you requested is not available.</p>

	<p>This could mean that the page has moved, or no longer exists.</p>

<?php

Theme::includeFooter();
