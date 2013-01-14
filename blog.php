<?php
require ("Page.php");

$page = new Page();

$page->title = "What's New at Earth Roots";
$page->content = '';

@ $db = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

$query = "SELECT * FROM blog ORDER BY post_when DESC LIMIT 5";
$result = $db->query($query);
if(!$result) {
  echo "Error, the site cannot be displayed.";
  exit;
}
$num_rows = $result->num_rows;
$i;
for($i=0;$i < $num_rows;$i++) {
  $row = $result->fetch_assoc(); //grabs next returned row from result
  $postName = $row['name'];
  $postContent = $row['content'];
  $postTime = $row['post_when'];
  
  $page->content .= <<<HTML
    <h1> $postName </h1>
    <br/>
    $postContent
    <br/>
    $postTime
    <br/>
    <br/>
    <div class="breaker"></div>
HTML;
}

$page->display();

?>
