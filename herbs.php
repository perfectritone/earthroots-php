<?php
require("Page.php");
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

//page instance
$page = new Page();

@ $name = trim($_GET['name']);

@ $db = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

if ($db->connect_error) {
  echo 'Error: Could not connect to database. Please try again later.';
  exit;
}
$db->set_charset("utf8"); //SET THIS IN MYSQL

if(!(empty($name))) {
  $name = Page::protectDB($name,$db);
  
  $query = 'select * from herbs where name="'.$name.'"';
  $result = $db->query($query);
  if($result->num_rows) {
    $row = $result->fetch_assoc();
    $herbName = ucwords(str_replace('+',' ',$row['name']));
    $page->content = herbContent($herbName,$result);
  } else $page->content = herbDefaultContent();
} else {
  $page->content = herbDefaultContent();
}

function herbContent($name,&$result) {
  $basics = getBasics();
  //Create a set of vars, which can be traits, description, history, traditional uses
  foreach( (array)$basics as $key => $value ) {
    $$key = $value;
  }
  $taxonomy = getTaxonomy();
  $alternativeNames = getAltNames();
  $ailments = getAilments();
  $active_chemicals = getActiveChemicals();
  $actions = getActions();
  $harvest = getHarvest();
  $pictureURLs = getPictureURL();
  $warnings = getWarnings();

  $content = <<<HTML
  <h1 class="content_header"><h1>$name</h1></div>
HTML;
  if(!empty($taxonomy))
    $content .= <<<HTML
  <h3>$taxonomy[family] $taxonomy[genus] $taxonomy[species]</h3><br/>
HTML;
  if(!empty($alternativeNames)) {
    $formattedNames = implode(', ',$alternativeNames);
    $content .= <<<HTML
  Also known as: $formattedNames.<br/>
HTML;
  }
  
  if(!empty($traits)) {
    $content .= <<<HTML
  <h2> Traits </h2>
  $traits
HTML;
  }
  if(!empty($description)) {
    $content .= <<<HTML
  <h2> Description </h2>
  $description
HTML;
  }  
  if(!empty($history)) {
    $content .= <<<HTML
  <h2> History </h2>
  $history
HTML;
  }
  if(!empty($traditional_uses)) {
    $content .= <<<HTML
  <h2> Traditional Uses</h2>
  $traditional_uses
HTML;
  }
  if(!empty($ailments)) {
    $ailments = implode(', ',$ailments);
    $content .= <<<HTML
  <h2> Ailments</h2>
  $ailments.
HTML;
  }
  if(!empty($active_chemicals)) {
    $active_chemicals = implode(', ',$active_chemicals);
    $content .= <<<HTML
  <h2> Active Chemicals</h2>
  $active_chemicals.
HTML;
  }
  if(!empty($actions)) {
    $actions = implode(', ',$actions);
    $content .= <<<HTML
  <h2> Actions</h2>
  $actions.
HTML;
  }
  if(!empty($harvest)) {
    $content .= <<<HTML
  <h2> Harvest</h2>
HTML;
    foreach( $harvest as $key => $value ) {
      $value = rtrim($value);
      if (substr($value, -1) != '.') $value << '.';
      $key = ucwords(
        str_replace('_',' ',
          str_replace('harvest','',$key)
        )
      );
      $content .= <<<HTML
  <p><strong> $key: </strong> $value </p>
HTML;
    }
  }
  if(!empty($pictureURLs)) {
    foreach($pictureURLs as $URL) {
      $content .= <<<HTML
  <img src=$link />
HTML;
    }
  }
  if(!empty($warnings)) {
    $content .= <<<HTML
  <h2> Warnings</h2>
HTML;
    foreach( $warnings as $key => $value ) {
      $value = rtrim($value);
      if (substr($value, -1) != '.') $value << '.';
      $key = ucwords(
        str_replace('_',' ',
          str_replace('warnings','',$key)
        )
      );
      $content .= <<<HTML
  <p><strong> $key: </strong> $value </p>
HTML;
    }
  }
  
  return $content;
}
function getBasics() {
  global $db, $name;
  
  $query = "select * from herbs where name='$name'";
  $result = $db->query($query);
  $row = $result->fetch_assoc();
  unset($row['name']); //remove name to avoid overwriting
  
  return $row;
}

function getTaxonomy() {
  global $db, $name;
  
  $query = "select * from taxonomy where name='$name'";
  $result = $db->query($query);
  $row = $result->fetch_assoc();
  
  return $row;
}

function getAltNames() {
  global $db, $name;
  
  $query = "select * from alternative_names where def_name='$name'";
  $result = $db->query($query);
  while ($row = $result->fetch_assoc()) {
  //for($i=0; $i < $result->num_rows; $i++) {
    //$row = $result->fetch_assoc();
    $names[] = $row['alt_name'];
  }
  if(!empty($names)) return $names;
}

function getAilments() {
  global $db, $name;
  
  $query = "select * from ailments where name='$name'";
  $result = $db->query($query);
  while ($row = $result->fetch_assoc()) {
    $ailments[$row['ailment']] = $row['effective_weight'];
  }
  if (!empty($ailments)) {
    krsort($ailments);
    return $ailments;
   } else return null;
}

function getActiveChemicals() {
  global $db, $name;
  
  $query = "select * from active_chemicals where name='$name'";
  $result = $db->query($query);
  while ($row = $result->fetch_assoc()) {
    $active_chemicals[] = $row['active_component'];
   }
   
   if(!empty($active_chemicals)) return $active_chemicals;
   else return null;
}

function getActions() {
  global $db, $name;
  
  $query = "select * from actions where name='$name'";
  $result = $db->query($query);
  while ($row = $result->fetch_assoc()) {
    $actions[] = $row['ailment_action'];
   }
   if(!empty($actions)) return $actions;
   else return null;
}

function getHarvest() {
  global $db, $name;
  
  $query = "select * from harvest where name='$name'";
  $result = $db->query($query);
  $row = $result->fetch_assoc();
  foreach( (array)$row as $key => $value ) {
    if (!empty($key)) $harvest[$key] = $value;
  }
    if(!empty($harvest)) {
      unset($harvest['name']);
      return $harvest;
    } else return null;
}

function getPictureURL() {
  global $db, $name;
  
  $query = "select * from picture where name='$name'";
  $result = $db->query($query);
  while( $row = $result->fetch_assoc()) {
    if(!empty($row['link'])) $pictures[] = $row['link'];
  }
  if(!empty($pictures)) return $pictures;
  else return null;
 }
 
function getWarnings() {
  global $db, $name;
  
  $query = "select * from warnings where name='$name'";
  $result = $db->query($query);
  $row = $result->fetch_assoc();
  foreach( (array)$row as $key => $value ) {
    if (!empty($key)) $warnings[$key] = $value;
   }
   if(!empty($warnings)) return $warnings;
   else return null;
}

function herbDefaultContent() {
  global $db;
  $content = <<<HTMLSTRING
  <div class="content_header">Herbs</div>
HTMLSTRING;
  
  $query = 'select * from herbs';
  $result = $db->query($query);
  $num_rows = $result->num_rows;
  $i;
  for($i=0;$i < $num_rows;$i++) {
    $row = $result->fetch_assoc(); //grabs next returned row from result
    $herbName = $row['name'];
    $herbNameField = strtolower(str_replace(' ','+',$herbName));
    $content .= <<<HTMLSTRING
    <a href="herbs.php?name=$herbNameField">$herbName</a>
    <br/>
    
HTMLSTRING;
  }
  
  return $content;
  
}

/*
$page->content = <<<HTML
  <dl> 
    <dt>$herb_name</dt>

HTML;
    
$query = 'select * from taxonomy where name="'.$herb_name.'"';
$result = $db->query($query)->fetch_assoc();
$page->content .= <<<HTML
    <dd>$result[family]</dd>
    <dd>$result[genus] $result[species]</dd>
HTML;

$page->content .= <<<HTML
  </dl>
HTML;
*/

$page->display();
?>
