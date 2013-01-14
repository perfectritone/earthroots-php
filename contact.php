<?php
require ("Page.php");

$page = new Page();

$page->title = "Shopping contact";

$page->content = <<<HTML
Contact: Addie McDermott
HTML;

$page->display();

?>
