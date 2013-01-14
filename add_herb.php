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
$newProduct = new Modify();

$inputs = array();
$name;

@ $db = new mysqli($dbHost, $adminUser, $adminPassword, $dbName);

if ($db->connect_errno) {
  echo 'Error: Could not connect to database. Please try again later.';
  exit;
}

$db->set_charset("utf8"); //SET THIS IN MYSQL

//Was anything posted?
if(!empty($_POST)) {
  //Make posted data safe for input into DB
  //This works because the table name = $groupName
  foreach($_POST as $groupName=>$groupVal) {
    foreach($groupVal as $inName=>$inVal) {
      if(is_scalar($inVal)) {
        $inputs[$groupName][$inName] = Page::protectDB($inVal,$db);
      }
      elseif(is_array($inVal)) {
        foreach($inVal as $inMultName=>$inMultVal) {
          $inputs[$groupName][$inName][$inMultName] = Page::protectDB($inMultVal,$db);
        }
      }
    }
  }
  
  unset($groupName);
  unset($groupVal);
  unset($inName);
  unset($inVal);
  unset($inMultName);
  unset($inMultVal);
  
  //Make sure the herb's name was input
  if(!($name = $inputs['herbs']['herb_name'])) {
      echo <<<HTML
You must fill out the name of the herb.<br/>
<a href="javascript:history.go(-1)">Go back</a>
HTML;
  exit;
  }
} else {
  echo <<<HTML
The form was not filled out.<br/>
<a href="javascript:history.go(-1)">Go back</a>
HTML;
  exit;
}

//Takes in a table and an array of the optional inputs, and inputs them
//after a quick check. Inputs must be in order for DB

function optionalInputs($table,$optIn) {
  global $db,$name;
  $isEmpty = 1;
  $lastLevel = 0;
  $toRet = 1;
  echo "<br>\$table:$table ";
  
  foreach($optIn as $option) {
    if(is_array($option)) {
      $toRet = $toRet && optionalInputs($table,$option);
      $lastLevel = 1;
    }
    $isEmpty &= empty($option);
  }
  unset($option);
  if($lastLevel) return 15;
  if($isEmpty) {return 1;} //all inputs are empty, do nothing
  
  $varsForQuery = implode("','",$optIn);
  if($table != 'herbs')
    $query = "insert into $table values ('$name','$varsForQuery')";
  else
    $query = "insert into $table values ('$varsForQuery')";
    
  $toRet = $db->query($query);
  //return $toRet?1:0;
  if($toRet) return 1;
  return 0;
}
$result = 1;

foreach($inputs as $groupName=>$groupInput) {
  $retVal = optionalInputs($groupName,$groupInput);
  echo "\$returned:$retVal";
  $result = $retVal && $result;
  if($result == 0 && $groupName == 'herb_name') break;
}
unset($groupName,$groupInput);


if($result) {
  $newProduct->content = <<<HTML
  You've successfully added $name to the Database!<br/><br/>
HTML
  .$newProduct->content;
} else {
  $newProduct->content = <<<HTML
  Sorry, $name could not be added to the Database. Please try again later or contact the system administrator.<br/><br/>
HTML
  .$newProduct->content;
}

$newProduct->display();

?>
