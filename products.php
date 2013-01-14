<?php
require ("Page.php");

//page instance
$page = new Page();

@ $name = trim($_GET['name']);
@ $cat = trim($_GET['cat']);

@ $db = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

if (mysqli_connect_errno()) {
  echo 'Error: Could not connect to database. Please try again later.';
  exit;
}
$db->set_charset("utf8"); //SET THIS IN MYSQL

if(!(empty($name))) {
  $name = Page::protectDB($name,$db);
  
  $query = 'select name from products where name="'.$name.'"';
  $result = $db->query($query);
  if($result->num_rows) {
    $page->content = productNameContent($name);
  } else {
    $page->content = "Sorry, we couldn't find that product.";
    $page->content .= productsContent();
  }
  
} else if(!(empty($cat))) {  
  $cat = Page::protectDB($cat, $db);  
  $query = 'select category from products where category="'.$cat.'"';
  $result = $db->query($query);
  if($result->num_rows) {
    $page->content = productCategoryContent($cat);
  } else {
    $page->content = productsContent();
  }
} else {
  $page->content = productsContent();
}


function productNameContent($name) {
  global $db;
  $name = ucwords(str_replace('+',' ',$name)); //unformat
  $content = <<<HTMLSTRING
  <div class="content_header">$name</div><br/>
HTMLSTRING;

  $query = 'select * from products where name="'.$name.'"';
  $result = $db->query($query);
  $row = $result->fetch_assoc();
  $rowCat = $row['category'];
  $rowPrice = $row['price'];
  $content .= <<<HTMLSTRING
  $rowCat<br/>
  $rowPrice<br/><br/>
  <dl>
  <dt>
  Ingredients:
  </dt>
HTMLSTRING;

  $query = 'select * from ingredients where name="'.$name.'"';
  $result = $db->query($query);
  $num_rows = $result->num_rows;
  $i;
  for($i=0;$i < $num_rows;$i++) {
    $row = $result->fetch_assoc();
    $ingredientName = $row['herb'];
    
    $ingred_check_query = 'select * from herbs where name="'.$ingredientName.'"';
    $ingred_check_result = $db->query($ingred_check_query);
    if($ingred_check_result) {
      $ingredientNameField = strtolower(str_replace(' ','+',$ingredientName));
      $content .= <<<HTMLSTRING
    <dd>
    <a href="herbs.php?name=$ingredientNameField">$ingredientName</a>
    </dd>
HTMLSTRING;
    }
  }
  
  $content .= <<<HTMLSTRING
  </dl>
HTMLSTRING;

  return $content;
}

function productCategoryContent($name) {
  global $db;
  $content = <<<HTMLSTRING
  <h1>$name</h1><br/>
HTMLSTRING;

  $query = 'select * from products where category="'.$name.'"';
  $result = $db->query($query);
  $num_rows = $result->num_rows;
  $i;
  for($i=0;$i < $num_rows;$i++) {
    $row = $result->fetch_assoc();
    $productName = $row['name'];
    $productNameField = strtolower(str_replace(' ','+',$productName));
    $content .= <<<HTMLSTRING
    <a href="products.php?name=$productNameField">$productName</a><br/><br/>
HTMLSTRING;
  }
}

function productsContent() {
  global $db;
  $content = <<<HTMLSTRING
  <h1 class="content_header">Products</h1>
HTMLSTRING;
  
  $query = 'select * from products';
  $result = $db->query($query);
  $num_rows = $result->num_rows;
  $i;
  for($i=0;$i < $num_rows;$i++) {
    $row = $result->fetch_assoc();
    $productName = $row['name'];
    $productNameField = strtolower(str_replace(' ','+',$productName));
    $content .= <<<HTMLSTRING
    <a href="products.php?name=$productNameField">$productName</a><br/><br/>
HTMLSTRING;
  }
  
  return $content;
}

if(!(empty($name))) $page->title = $name;

$page->display();

?>
