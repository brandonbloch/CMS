<?php

$page = CMS\Pages::getCurrentPage();

CMS\Theme::includeHeader();

echo CMS\Markdown::parse($page->getContent());

CMS\Theme::includeFooter();
