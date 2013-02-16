<?php

//eras variable, ideally this would all come from the actual categories
//$eras = get_categories(array('parent'=>20, 'hide_empty'=>false));

$eras = array(
	array(
		'category_id'=>2,
		'slug'=>'early_ny',
		'start_year'=>1650,
		'end_year'=>1830,
		'name'=>'Early New York',
		'title'=>'Family Poverty in Early New York',
		'url'=>'/eras/early-new-york-city/'
	),
	array(
		'category_id'=>1,
		'slug'=>'nineteenth',
		'start_year'=>1830,
		'end_year'=>1890,
		'name'=>'19th Century',
		'title'=>'Homelessness in the Nineteenth Century',
		'url'=>'/eras/19th-century/'
	),
	array(
		'category_id'=>3,
		'slug'=>'progressive',
		'start_year'=>1890,
		'end_year'=>1920,
		'name'=>'The Progressive Era',
		'title'=>'Homelessness in the Progressive Era',
		'url'=>'/eras/the-progressive-era/'
	),
	array(
		'category_id'=>35,
		'slug'=>'great_depression',
		'start_year'=>1929,
		'end_year'=>1944,
		'name'=>'The Great Depression',
		'title'=>'Family Crises in the Great Depression',
		'url'=>'/eras/the-great-depression/'
	),
	array(
		'category_id'=>4,
		'slug'=>'today',
		'start_year'=>1945,
		'end_year'=>'Today',
		'name'=>'Today',
		'title'=>'Origins of the New Urban Poverty',
		'url'=>'/eras/new-urban-poverty'
	)
);


//setup
//add_theme_support('menus');
add_filter('body_class', 'icph_body_class');
add_action('wp_ajax_browse', 'icph_browse');
add_action('wp_ajax_nopriv_browse', 'icph_browse');

//custom functions
function icph_body_class($classes)  {
	global $post;
	foreach(explode('/', $_SERVER['REQUEST_URI']) as $category) {
		if (!empty($category)) $classes[] = trim($category);
	}
	if ($post && $post->ID) {
		foreach((get_the_category($post->ID)) as $category) $classes[] = trim($category->category_nicename);
	}
	return array_unique($classes);
}

function icph_browse($type='Subject') {
	global $eras;
	
	$parent_id = (isset($_POST['type']) && ($_POST['type'] == 'policy')) ? 21 : 22;
	$categories = get_categories('parent=' . $parent_id);

	foreach ($categories as $category) {?>
		
	<div class="row">
		<h3<?php if ($category->name == $categories[0]->name) {?> class="first"<?php }?>><?php echo $category->name?></h3>
		<ul>
			<?php 
			$posts = get_posts('category=' . $category->term_id);
			foreach ($posts as $post) {
				//get post's era -- probably will replace this with multiple eras?
				$this_era = false;
				$post_categories = wp_get_post_categories($post->ID);
				foreach ($post_categories as $pc) {
					foreach ($eras as $era) {
						if ($pc == $era['category_id']) $this_era = '<h4 class="' . $era['slug'] . '">' . $era['name'] . '</h4>';
					}
				}
			?>
			<li>
				<img src="<?php bloginfo('template_directory');?>/img/placeholder/great-migration-circle.png" alt="great-migration-circle" width="150" height="150" />
				<div>
					<?php echo $this_era?>
					<a href="#<?php echo $post->post_name?>"><?php echo $post->post_title?></a>
					<p><?php echo $post->post_excerpt?> &hellip;</p>
				</div>
			</li>
			<?php }?>
		</ul>
	</div>
	
	<?php }

	if (isset($_POST['type'])) die(); //end output here on ajax requests
}

function icph_ul($elements, $arguments) {
	//universal lightweight method for outputting UL lists with first and last classes on LI elements
	$count = count($elements);
	$counter = 1;
	foreach ($elements as &$e) {
		$e['class'] = empty($e['class']) ? array() : explode(' ', $e['class']);
		if ($counter == 1) $e['class'][] = 'first';
		if ($counter == $count) $e['class'][] = 'last';
		$e['content'] = empty($e['link']) ? $e['content'] : '<a href="' . $e['link'] . '">' . $e['content'] . '</a>';
		$e = '<li class="' . implode(' ', array_unique($e['class'])) . '">' . $e['content'] . '</li>';
		$counter++;
	}
	
	$return = '<ul'; //assemble output
	foreach ($arguments as $key=>$value) $return .= ' ' . $key . '="' . $value . '"';
	return $return . '>' . implode('', $elements) . '</ul>';
}

/*
object(stdClass)#216 (15) { 
	["term_id"]=> &string(1) "3" 
	["name"]=> &string(64) "1890s - 1920s: Poverty & Homelessness in The Progressive Era" 
	["slug"]=> &string(9) "1890-1920" 
	["term_group"]=> string(1) "0" 
	["term_taxonomy_id"]=> string(1) "3" 
	["taxonomy"]=> string(8) "category" 
	["description"]=> &string(566) "Urbanization, immigration, and industrialization transformed New York City’s economy between 1890 and 1920, making poverty more prevalent among the working class while at the same time creating enormous wealth for some. Efforts to alleviate the effects of poverty among working-class and poor families through direct action and government reform become known as “progressivism.” The sights, sounds, and smells of the poorer districts in New York City in the 1890s made evident the effects of mass urbanization, immigration, and industrialization. " 
	["parent"]=> &string(2) "20" 
	["count"]=> &string(1) "7" 
	["cat_ID"]=> &string(1) "3" 
	["category_count"]=> &string(1) "7" 
	["category_description"]=> &string(566) "Urbanization, immigration, and industrialization transformed New York City’s economy between 1890 and 1920, making poverty more prevalent among the working class while at the same time creating enormous wealth for some. Efforts to alleviate the effects of poverty among working-class and poor families through direct action and government reform become known as “progressivism.” The sights, sounds, and smells of the poorer districts in New York City in the 1890s made evident the effects of mass urbanization, immigration, and industrialization. " 
	["cat_name"]=> &string(64) "1890s - 1920s: Poverty & Homelessness in The Progressive Era" 
	["category_nicename"]=> &string(9) "1890-1920" 
	["category_parent"]=> &string(2) "20" 
}
*/