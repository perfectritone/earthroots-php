<?php
require ("Page.php");

$page = new Page();

$page->title = "Shopping links";

$page->content = <<<HTML
Check out my friends' websites!
HTML;

$page->display();

?>
