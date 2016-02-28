<?php

use \CMS\Core as Core;
use \CMS\Site as Site;

?><footer>
	<p>&copy; <a href="mailto:<?php echo Site::getAdminEmail() ?>"><?php echo Site::getAdminName() ?></a></p>
</footer>

<?php Core::includeCore() ?>

</body>
</html>