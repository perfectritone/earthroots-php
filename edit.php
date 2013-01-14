<?php
require("Modify.php");

$page = new Modify();

@ $db = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

if ($db->connect_errno) {
  echo 'Error: Could not connect to database. Please try again later.';
  exit;
}

$db->set_charset("utf8"); //SET THIS IN MYSQL

@ $username = Page::protectDB(trim($_POST['username']),$db);
@ $password = Page::protectDB(trim($_POST['password']),$db);

if (!$username || !$password) {
  echo "Sorry, you left a field blank.";
  exit;
}

$query = "SELECT password, salt
    FROM users
    WHERE username = '$username';";
$result = $db->query($query);

if($result->num_rows < 1) //no such user exists
{
  echo <<<HTML
User '$username' not found. <br>
<a href = "javascript:history.back();">back</a>
HTML;
  die;
}

$row = $result->fetch_assoc();
$hash = hash('sha256', $row['salt'] . hash('sha256', $password));

if($hash != $row['password']) //incorrect password
{
  echo <<<HTML
Wrong password. <br>
<a href = "javascript:history.back();">back</a>
HTML;
  die;
}

session_start();

if (!isset($_SESSION['initiated']))
{
  session_regenerate_id();
  $_SESSION['initiated'] = true;
}
$_SESSION['admin'] = true;

$page->display();

?>
