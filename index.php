<?php
require ("Page.php");

$page = new Page();

$db = new mysqli( $dbHost, $dbUser, $dbPassword, $dbName);
if ($db->connect_error) {
  die($db->connect_error.' Error #'.$db->connect_errno);
}

$page->content = <<<HTMLSTRING
  <h1>Welcome!</h1>
  <form action="searchailment.php" method="get">
    <select class="select_ailment" name="ailment">
HTMLSTRING;

foreach (possibleAilments() as $ailment) {
  $page->content .= <<<HTMLSTRING
  <option value='$ailment'>$ailment</option>
HTMLSTRING;
}

$page->content .= <<<HTMLSTRING
    </select>
    <input type="submit" value="Search"/>
  </form>  
HTMLSTRING;

$page->display();

function possibleAilments() {
  global $db;

  $query = 'select ailment from ailments';
  $result = $db->query($query);
  $num_rows = $result->num_rows;

  $options = array(); //or other sorted data structure

  for ($i=0; $i < $num_rows; $i++) {
    $row = $result->fetch_assoc();
    $ailment = ucwords($row['ailment']);
    if (!array_key_exists($ailment, $options)) {
      $options[] = $ailment;
    }
  }
  
  sort($options);
  return $options;
}
?>
