<?php

use \CMS\Pages as Pages;
use \CMS\Theme as Theme;

$page = Pages::getCurrentPage();

Theme::includeHeader();

?>


<div class="page">
	<div class="main"><?php echo $page->getZoneOutput(0); ?></div>
	<div class="sidebar sidebar-right">
		<h2>Sidebar</h2>
		<?php echo $page->getZoneOutput(1); ?>
	</div>
</div>


<?php

Theme::includeFooter();
