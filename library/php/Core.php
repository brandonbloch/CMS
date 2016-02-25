<?php

class Core {

	/**
	 *
	 */
	public static function includeCore() {
		echo '<link rel="stylesheet" href="style.css">' . PHP_EOL;
		echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">' . PHP_EOL;
		include "actions/adminbar.php";
	}

}