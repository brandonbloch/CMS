<?php

namespace CMS;

class Core {

	public static function includeCore() {
		echo '<link rel="stylesheet" property="stylesheet" href="' . Site::getBaseURL() . '/library/css/style.css">' . PHP_EOL;
		echo '<link rel="stylesheet" property="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">' . PHP_EOL;
		include "actions/adminbar.php";
		echo '<script src="' . Site::getBaseURL() . '/library/js/autosize.min.js"></script>' . PHP_EOL;
		echo '<script>' . PHP_EOL;
		echo 'var textareas = document.querySelector(\'textarea\');' . PHP_EOL;
		echo 'autosize(textareas);' . PHP_EOL;
		echo 'autosize.destroy(document.querySelectorAll(\'.no-autosize\'));' . PHP_EOL;
		echo '</script>' . PHP_EOL;
	}

}