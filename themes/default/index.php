<?php

$page = Pages::getCurrentPage();

Theme::includeHeader();

echo Markdown::parse($page->getContent());

Theme::includeFooter();
