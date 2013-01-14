<?php

session_start();

require("Modify.php");

if (!isset($_SESSION['admin'])) {
  echo <<<HTML
<br>
Sorry, you don't have permission to view this site. Try logging in again.
<a href = "javascript:history.back();">back</a>
HTML;
  die;
} elseif ($_SESSION['admin'] != true) {
  echo <<<HTML
Sorry, you don't have permission to view this site.<br>
Try logging in with a different account.
<a href = "javascript:history.back();">back</a>
HTML;
  die;
}

//page instance
$page = new Modify();

@ $db = new mysqli($dbHost, $adminUser, $adminPassword, $dbName);

if ($db->connect_errno) {
  echo $dbHost . $adminUser . $adminPassword . $dbName;
  echo 'Error: Could not connect to database. Please try again later.';
  exit;
}

$db->set_charset("utf8"); //SET THIS IN MYSQL

if(!empty($_POST)) {
  //Make posted data safe for input into DB
  //This works because the table name = $groupName
  $name =  Page::protectDB($_POST['name'],$db);
  $content = Page::protectDB($_POST['content'],$db);
  if(empty($name) || empty($content)) {
    echo <<<HTML
The blog must have both a title and contents.<br/>
<a href="javascript:history.go(-1)">Go back</a>
HTML;
    exit;
  }
} else {
  echo <<<HTML
Nothing was entered.<br/>
<a href="javascript:history.go(-1)">Go back</a>
HTML;
  exit;
}

$query = "insert into blog values ('$name','$content',now())";
$result = $db->query($query);

if($result) {
  $page->content = <<<HTML
  You've successfully posted the blog '$name'!<br/><br/>
HTML
  .$page->content;
} else {
  $page->content = <<<HTML
  Sorry, '$name' could not be posted. Please try again later<br/><br/>
HTML
  .$page->content;
}

$page->display();

?>
