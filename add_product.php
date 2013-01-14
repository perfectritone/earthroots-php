<?php
require("Modify.php");

session_start();
if (!isset($_SESSION['admin'])) {
  echo <<<HTML
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

$inputs = array();
$name;

//open db for this page's input
@ $db = new mysqli($dbHost, $adminUser, $adminPassword, $dbName);

if ($db->connect_errno) {
  echo 'Error: Could not connect to database. Please try again later.';
  exit;
}

$db->set_charset("utf8"); //SET THIS IN MYSQL

if(!empty($_POST)) {
//Get data from the POST
  foreach($_POST as $productName=>$productVal) {
    if(is_scalar($productVal)) {
      $inputs[$productName] = Page::protectDB($productVal,$db);
    }
    elseif(is_array($productVal)) {
      foreach($productVal as $multName=>$multVal) {
        $inputs[$productName][$multName] = Page::protectDB($multVal,$db);
      }
    }
  }
  unset($productName,$productVal,$multName,$multVal);
  $name = $inputs['product_name'];
} else {
  echo <<<HTML
The form was not filled out.<br/>
<a href="javascript:history.go(-1)">Go back</a>
HTML;
  exit;
}

foreach($inputs as $in) {
  if(empty($in)) {
      echo <<<HTML
You must fill out all of the forms.<br/>
<a href="javascript:history.go(-1)">Go back</a>
HTML;
  exit;
  }
}
unset($in);

//Validate price format
$price = $inputs['price'];
if (!is_numeric($price)) {
  if (is_string($price)) {
    $price = str_replace('$','',$price);
    $price = (float) $price;
    if (!$price) { //if the conversion to float is unsuccessful
      echo <<<HTML
Your inputted price was invalid.<br/>
<a href="javascript:history.go(-1)">Go back</a>
HTML;
      exit;
    }
  } else {
    echo "Something is really wrong here.";
    exit;
  }
}
if (!preg_match('/\d+.?\d{0,2}/',$price)) {
  echo <<<HTML
Your inputted price was invalid.<br/>
<a href="javascript:history.go(-1)">Go back</a>
HTML;
  exit;
}


$cat = $inputs['category'];
$price = $inputs['price'];
$query = "insert into products values ('$name','$cat',$price)";
$result = $db->query($query);
if(!$result) {
  echo <<<HTML
This didn't work. Maybe you have already entered this product before.<br/>
Go to the <a href="#">edit products</a> page to check.<br/>
<a href="javascript:history.go(-1)">Go back</a>
HTML;
  exit;
}

foreach($inputs['ingredients'] as $herb) {
  $ingredientsQuery = "insert into ingredients values ('$name','$herb')";
  $ingredientsResult = $db->query($ingredientsQuery) && $ingredientsResult;
  
}
unset($herb);


if($result) {
  $page->content = <<<HTML
  <span class="result">You've successfully added $name to the Database!</span><br/><br/>
HTML
  .$page->content;
} else {
  $page->content = <<<HTML
  <span class="result">Sorry, $name could not be added to the Database. Please try again later.</span><br/><br/>
HTML
  .$page->content;
}

$page->display();

?>
