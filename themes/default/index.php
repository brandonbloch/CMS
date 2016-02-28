<?php

use \CMS\Pages as Pages;
use \CMS\Theme as Theme;

$page = Pages::getCurrentPage();

Theme::includeHeader();

echo $page->getZoneOutput(0);

Theme::includeFooter();
