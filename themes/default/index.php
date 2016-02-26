<?php

$page = CMS\Pages::getCurrentPage();

CMS\Theme::includeHeader();

echo CMS\Library\Markdown::parse($page->getContent());

CMS\Theme::includeFooter();
