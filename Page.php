<?php

error_reporting(0);

include("../external/db.php");

class Page {

  // class Page's attributes
  public $content;
  public $title = "Earth Roots";
  public $style = "style.css";
  public $keywords = "Earth Roots, Herbalism, Asheville, North Carolina, Herbs";
  public $topButtons = array("Home"   => "/", 
             "Blog" => "blog.php", 
             "Links" => "links.php", 
             "Basket" => "basket.php",
             "Contact" => "contact.php"
  );
  public $sideButtons = array(
    'Products'=>array(
      'Organic Body Care'=>array(
        'Body Butters'=>NULL,
        'Salves'=>NULL,
        'Bath Salts'=>NULL,
        'Clay Masks'=>NULL
      ),
      'Organic Teas'=>NULL,
      'Aromatherapy'=>NULL
    ),
    'Herbs'=>NULL,
    'Recipes'=>NULL
  );

  // class Page's operations

  public function display() {
    echo "<!DOCTYPE html>\n<html>\n<head>\n";
    $this -> displayTitle();
    echo "\t<meta charset=\"utf-8\"/>\n";
    $this -> displayKeywords();
    $this -> displayStyles();
    //$this -> displayScripts();
    echo "</head>\n<body>\n<div id=\"foreground\">\n";
    $this -> displayHeader();
    $this -> displayTopMenu($this->topButtons);
    $this -> displayMenuDivider();
    $this -> displaySideMenu($this->sideButtons);
    echo "\n\t<div id=\"content\">\n";
    echo $this->content;
    echo "</div>\n";
    $this -> displayFooter();
    echo "</div>\n</body>\n</html>\n";
  }

  public function displayTitle() {
    echo "\t<title>".$this->title."</title>\n";
  }

  public function displayKeywords() {
    echo "\t<meta name=\"keywords\" content=\"".$this->keywords."\"/>";
  }

  public function displayStyles() {  
    $tempStyle = $this->style;
    echo <<<HTML
    
  <link rel="stylesheet" href="$tempStyle" />
  
HTML;
  }
  
  public function displayScripts() {
    //http://ajax.googleapis.com/ajax/libs/jquery/1.7.2
?>
  <script src="jquery.min.js"></script>
  <script>
    
    $(document).ready(function() {
      
      
      function resizeSidebar() {
        var inside_box_heights = [$('#content').height(),
          $('#content').css('padding-bottom'),
          $('#content').css('padding-top')];
        var inside_box_height = 0, i = 0;
        for (i; i < inside_box_heights.length; i++) {
          if (typeof inside_box_heights[i] === "string")
            inside_box_heights[i] = parseFloat(inside_box_heights[i]);
          inside_box_height += inside_box_heights[i];
        }
        $('#sideMenu').css("height", inside_box_height);
      }

      var foreground_width = $('#header img').width();
      $('#foreground').css("width", foreground_width);
      $('#topMenu table').css("width", foreground_width);
      var side_width = 158; //$('#sideMenu').width());
      $('#content').css("margin-left", side_width);
      
      $('#content').css("min-height", $('#sideMenu').height());
      resizeSidebar();
      $('body').height($(window).height()*.95);
      
      $('#loginForm').hide();
      
      $('#loginButton').click(function() {
        $('#loginForm').toggle();
        $('#loginForm input[name="username"]').focus();
      });
    });
    
  </script>
<?php
}

  public function displayHeader() { 
?>   
<div id="header">
  <h1><a href='/'><img src="logo.png" alt="Earth Roots" /></a></h1>
</div>
<?php
  }
  
  public function displayButton($width,$name,$url,$first) {
    if( !$first ) {
      echo <<<HTML
      
      <td width="$width%">
        <a href="$url"> 
          $name
        </a>
      </td>
HTML;
    } else {
      echo <<<HTML
      
      <td id="first_button" width="$width%">
        <a href="$url"> 
          $name
        </a>
      </td>
HTML;
    }
    
    $image_buttons = <<<HTML
    
    <td width = "$width%">
    <a href="$url">
    <img height=30px width=60px src="s-logo.gif" alt="$name" title="$name" border="0" /></a>
    </td>
HTML;
  }

  public function displayTopMenu($buttons) {
    echo "\n<div id=\"top_menu\">";
    echo "\n\t<table>\n\t\t";
    echo "<tr>\n";

    //calculate button size
    $width = 100/count($buttons);

    $name = array_keys($buttons);
    $name = $name[0];
    $url = array_shift($buttons);
    
    $this -> displayButton($width, $name, $url, true);
    
    while (list($name, $url) = each($buttons)) {
      $this -> displayButton($width, $name, $url, false);
    }
    echo <<<HTML
    </tr>
    </table>
    </div>
HTML;
  }
  
  /*
  public function buttonsToBar($item,$key) {
    echo $item->getName();
  }
  */
  
  public function displayMenuDivider() {
    echo <<<HTML
    
    <div id="menu_divider"></div>
HTML;
  }
  
  public function displaySideMenu($buttons) {

    echo <<<HTML
    
<div id="sideMenu">
  <ul>\n
HTML;
    
    $tempLev1;
    $tempLev2;
    $tempLev3;
    
    foreach($buttons as $buttonName=>$buttonArr) {
       //This button is a top category or not in the hierarchy
      if($buttonArr == NULL || is_array($buttonArr)) {
        $tempLev1 = strtolower(str_replace(' ','',$buttonName));
        $image_path = "images/$tempLev1.png";
        if(file_exists($image_path))
          $buttonName = "<img src=\"$image_path\" />";
        echo <<<HTML
      <li><a href="$tempLev1.php">$buttonName</a></li>
HTML;
        if($buttonArr != NULL) {
          foreach((array)$buttonArr as $catName=>$catArr) {
            $tempLev2 = strtolower(str_replace(' ','',str_replace('Organic','',$catName)));
            $image_path = "images/$tempLev2.png";
            if(file_exists($image_path))
              $catName = "<img src=\"$image_path\" />";
            $field = ($buttonName == 'Products')? 'cat':'name';
            echo <<<HTML
      <li><a href="$tempLev1.php?$field=$tempLev2">$catName</a></li>
HTML;
            if(is_array($catArr)) { //This button is a secondary category
              foreach((array)$catArr as $prodName=>$prodArr) {
                $tempLev3 = strtolower(str_replace(' ','',$prodName));
                $image_path = "images/$tempLev3.png";
                if(file_exists($image_path))
                  $prodName = "<img src=\"$image_path\" />";
                echo <<<HTML
    <li><a href="$tempLev1.php?name=$tempLev3">$prodName</a></li>
HTML;
              }
            }
          }
        }
      } else echo <<<HTML
      <li><a href="$buttonArr">$buttonName</a></li>
HTML;
    }
    
    echo <<<HTML
    
  </ul>
</div>
HTML;
    
  }

  public function displayFooter() {
?>
<div id="footer">
  <table>
    <tr id="loginForm">
        <td>
          <form action="edit.php" method="post">
            Please enter your username: <input id="u" name="username" type="text"/>
        </td>
        <td>
            and password: <input name="password" type="password"/>
            <input type="submit" value="Submit"/>
          </form>
        </td>
    </tr>
    <tr>
      <td><a id="loginButton" href="javascript:;">login</a></td>
      <td>
      &copy;Earth Roots, 2012.
      </td>
    </tr>
  </table>
</div>
<?php
  }
  
  //Protect the DB from SQL injection
  static function protectDB ($input,&$db) {  
    if(ini_get('magic_quotes_gpc'))
      $input = stripslashes($input);
    $input = $db->real_escape_string(trim($input));
    
    return $input;
  }
}
?>
