<?php
require ("Page.php");

class Modify extends Page {
	public function displayScripts() {
		parent::displayScripts();
?>
	<script src="jquery.ba-resize.min.js"> </script>
	<script>
		$(document).ready(function() {
      
			function moreInputs (input) {
				$('.'+input).last().clone(true, true).val('').insertAfter($('.'+input).last()).before('<br>');
			} 
			function moreInputsTab (event) {
				if (event.keyCode === 9) {
					$('#more_'+event.data.i).click();
					$('.'+event.data.i).last().focus();
					event.preventDefault();
				}
			}
			function more_listeners(input) {
				$('#more_'+input).click(function() {
					moreInputs(input)
          resizeSidebar();
				});
				$(document).on('keypress','.'+input, {i: input}, moreInputsTab);
			}
      
      function focus_listeners(input) {
        var a = 1;
      }
			
			//$('#content').resize(resizeSidebar);
			
			more_listeners('ingredients');
			more_listeners('alternative_names');
			//listeners('ailments');
			more_listeners('actions');
			more_listeners('active_chemicals');
		});
	</script>
<?php
	}
	
	public $title = "Edit the Database";
  
  public $style = "modify.css";
	
	public $sideButtons = array(
		"Edit Blog"=>"#add_blog",
		"Add a Product"=>"#add_product",
		"Add an Herb"=>"#add_herb"
	);

	
	public $content = <<<HTML
    <form id="add_blog" method="post" action="add_blog.php">
      Post a new blog entry below
      <fieldset>
        Title: <input type="text" name="name" /><br/>
        What to say: <textarea name="content"></textarea><br/>
      </fieldset>
      <input type="submit" value="submit" />
    </form>
    
    <br/><br/>
    
		<form id="add_product" method="post" action="add_product.php">
			Add a new product below<br/>
			<br/>
      <fieldset>
			Product name: <input type="text" name="product_name" /><br/>
			Category: <input type="text" name="category" /><br/>
			Price: $<input type="text" name="price"  /><br/>
			Ingredients (herbs): <br/>
			
				<input type="text" name="ingredients[]" class="ingredients" /><br/>
						
			<a href="javascript:;" id="more_ingredients">+</a>

			<br/>
      </fieldset>
			<input type="submit" value="submit" />
		</form>
		<br/><br/>
		<form id="add_herb" method="post" action="add_herb.php">
			Add new herbs below<br/>
			
			<fieldset>
			Herb name: <input type="text" name="herbs[herb_name]" /><sup>*</sup><br/>
			traits of the herb: <textarea name="herbs[traits]"></textarea><br/>
			description of the herb: <textarea name="herbs[description]"></textarea><br/>
			history: <textarea name="herbs[history]"></textarea><br/>
			traditional uses: <textarea name="herbs[traditional_uses]"></textarea><br/>
			</fieldset>
			
			<fieldset>
			Other names: <br/>
			
				<input type="text" name="alternative_names[][alternative_names]" class="alternative_names" /><br/>
			
			<a href="javascript:;" id="more_alternative_names">+</a>
			</fieldset>
			
			<fieldset>
			family: <input type="text" name="taxonomy[family]" /><br/>
			genus: <input type="text" name="taxonomy[genus]" /><br/>
			species: <input type="text" name="taxonomy[species]" /><br/>
			</fieldset>
			
			<fieldset>
				<div class="ailments">
					ailment name: <input type="text" name="ailments[][ailments]" /><br/>
					effectiveness from 1-100: <input type="text" name="ailments[][effective_weight]" /><br/>
				</div>
			</fieldset>
			
			<fieldset>
			active components: <br/>
			
				<input type="text" name="active_chemicals[][active_chemicals]" class="active_chemicals" /><br/>
				
			<a href="javascript:;" id="more_active_chemicals">+</a>
			</fieldset>
			
			<fieldset>
			actions: <br/>
			
				<input type="text" name="actions[][actions]" class="actions" /><br/>
				
			<a href="javascript:;" id="more_actions">+</a>
			</fieldset>
			
			<fieldset>
			<br/>

			harvest time: <textarea name="harvest[harvest_when]"></textarea><br/>
			where it grows: <textarea name="harvest[place]"></textarea><br/>
			what part of the plant is used: <textarea name="harvest[plant_part]"></textarea><br/>
			how to harvest: <textarea name="harvest[method]"></textarea><br/>
			</fieldset>
			
			<input type="submit" value="submit" />
      </form>
HTML;
}	
?>
