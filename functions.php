<?php
/*
Author: Eddie Machado
URL: htp://themble.com/bones/

This is where you can drop your custom functions or
just edit things like thumbnail sizes, header images,
sidebars, comments, ect.
*/
mb_internal_encoding('UTF-8');
define('TRANSIENT_LENGTH', 360);

// LOAD BONES CORE (if you remove this, the theme will break)
require_once 'library/bones.php';

// CUSTOMIZE THE WORDPRESS ADMIN (off by default)
require_once 'library/admin.php';

/*********************
LAUNCH BONES
Let's get everything up and running.
*********************/

function bones_ahoy() {

	//Allow editor style.
	add_editor_style();

	// let's get language support going, if you need it
	load_theme_textdomain( 'bonestheme', get_template_directory() . '/library/translation' );

	// launching operation cleanup
	add_action( 'init', 'bones_head_cleanup' );
	// A better title
	add_filter( 'wp_title', 'rw_title', 10, 3 );
	// remove WP version from RSS
	add_filter( 'the_generator', 'bones_rss_version' );
	// remove pesky injected css for recent comments widget
	add_filter( 'wp_head', 'bones_remove_wp_widget_recent_comments_style', 1 );
	// clean up comment styles in the head
	add_action( 'wp_head', 'bones_remove_recent_comments_style', 1 );
	// clean up gallery output in wp
	add_filter( 'gallery_style', 'bones_gallery_style' );

	// enqueue base scripts and styles
	add_action( 'wp_enqueue_scripts', 'bones_scripts_and_styles', 999 );
	// ie conditional wrapper

	// launching this stuff after theme setup
	bones_theme_support();

	// adding sidebars to Wordpress (these are created in functions.php)
	//add_action( 'widgets_init', 'bones_register_sidebars' );

	// cleaning up random code around images
	add_filter( 'the_content', 'bones_filter_ptags_on_images' );
	// cleaning up excerpt
	add_filter( 'excerpt_more', 'bones_excerpt_more' );

} /* end bones ahoy */

// let's get this party started
add_action( 'after_setup_theme', 'bones_ahoy' );


/************* OEMBED SIZE OPTIONS *************/

if ( ! isset( $content_width ) ) {
	$content_width = 640;
}

/************* THUMBNAIL SIZE OPTIONS *************/

// Thumbnail sizes
add_image_size( 'bones-thumb-600', 600, 150, true );
add_image_size( 'bones-thumb-300', 300, 100, true );

/*
to add more sizes, simply copy a line from above
and change the dimensions & name. As long as you
upload a "featured image" as large as the biggest
set width or height, all the other sizes will be
auto-cropped.

To call a different size, simply change the text
inside the thumbnail function.

For example, to call the 300 x 100 sized image,
we would use the function:
<?php the_post_thumbnail( 'bones-thumb-300' ); ?>
for the 600 x 150 image:
<?php the_post_thumbnail( 'bones-thumb-600' ); ?>

You can change the names and dimensions to whatever
you like. Enjoy!
*/

add_filter( 'image_size_names_choose', 'bones_custom_image_sizes' );

function bones_custom_image_sizes( $sizes ) {
	return array_merge( $sizes, array(
			'bones-thumb-600' => __('600px by 150px'),
			'bones-thumb-300' => __('300px by 100px'),
		) );
}


/*
The function above adds the ability to use the dropdown menu to select
the new images sizes you have just created from within the media manager
when you add media to your content blocks. If you add more image sizes,
duplicate one of the lines in the array and name it according to your
new image size.
*/



/*
This is a modification of a function found in the
twentythirteen theme where we can declare some
external fonts. If you're using Google Fonts, you
can replace these fonts, change it in your scss files
and be up and running in seconds.
*/
function bones_fonts() {
	wp_register_style('googleFonts', 'http://fonts.googleapis.com/css?family=Nixie+One|Source+Code+Pro:300,400,700');
	wp_enqueue_style( 'googleFonts');
}


add_action('wp_print_styles', 'bones_fonts');


//require 'functions.tisse-la-toile.php';

function wp_get_all_tags( $args = '' ) {

	$tags = get_terms('post_tag');
	foreach ( $tags as $key => $tag ) {
		if ( 'edit' == 'view' )
			$link = get_edit_tag_link( $tag->term_id, 'post_tag' );
		else
			$link = get_term_link( intval($tag->term_id), 'post_tag' );
		if ( is_wp_error( $link ) )
			return false;

		$tags[ $key ]->link = $link;
		$tags[ $key ]->id = $tag->term_id;
		$tags[ $key ]->name = $tag->name;
		//      echo ' <a href="'. $link .'">' . $tag->name . '</a>';
	}
	return $tags;
}


// AJAX FOR DATAVIZ

add_action( 'wp_ajax_nopriv_tisse_la_toile', 'tisse_la_toile_tag_request' );
add_action( 'wp_ajax_tisse_la_toile', 'tisse_la_toile_tag_request' );

function tisse_la_toile_posts_request() {

	// get all posts and tags as json
	$all_posts = get_transient('toile_all_posts_ever');
	if( false === $all_posts ) {
		$args=array(
			'post_type' => 'post',
			'post_status' => 'publish',
			'posts_per_page' => -1
		);
		$all_posts = null;
		$all_posts = new WP_Query($args);
		set_transient( 'toile_all_posts_ever', $all_posts, 12 * TRANSIENT_LENGTH );
	}

	if( $all_posts->have_posts() ) {
		//echo json_encode($all_posts->get_posts());

		$array = array();

		while ( $all_posts->have_posts() ) : $all_posts->the_post();
		$array[] = array(
			'id' => get_the_ID(),
			'url'=> get_permalink(),
			'name' => get_the_title(),
			'children' => get_the_tags()
		);
		endwhile;
		wp_reset_query();
		echo json_encode($array);
	}

	// Always die in functions echoing ajax content
	die();
}


function tisse_la_toile_tag_request_1() {

	// get all posts and tags as json
	$return = array();
	$tags = wp_get_all_tags();

	foreach ($tags as $tag){

		$children =array();
		$args=array(
			'tag' => $tag->name,
			'showposts'=>500,
			'ignore_sticky_posts'=>1
		);
		$my_query = new WP_Query($args);
		if( $my_query->have_posts() ) {
			while ($my_query->have_posts()) : $my_query->the_post();
				$child_tags = get_the_tags();
				$child_tags_r=array();
				if ($child_tags) {
					foreach($child_tags as $stag) {
						$child_tags_r[]= array("name"=>$stag->name, "count"=> $stag->count);
					}
				}
				$children[]= array(
					'url'=> get_permalink(),
					'name' => get_the_title(),
					"type"=>"article",
					"count"=> count($child_tags_r),
					'children' => $child_tags_r
				);
			endwhile;
		}

		$return[]= array("name"=>$tag->name, "type"=>"tag", "count"=> $tag->count, "children"=>$children);
	}
	$return =array("name" => "Histoire du Web", "children"=>$return);
	wp_reset_query();
	echo json_encode($return);
	die();
}


function tisse_la_toile_tag_request() {

	// get all posts and tags as json
	$return = array();
	$tags = wp_get_all_tags();

	foreach ($tags as $tag){


		

		$args=array(
			'tag' => $tag->name,
			'showposts'=>500,
			'ignore_sticky_posts'=>1
		);
		$my_query = new WP_Query($args);

		if( $my_query->have_posts() ) {

			$return[]= array("source"=>"Histoire du Web", "target"=>$tag->name, "type"=>"tag");
			
			while ($my_query->have_posts()) : $my_query->the_post();
			
				$return[]=array("source"=> $tag->name, "target"=> get_the_title(), "type"=>"article", 'url'=> get_permalink());

			endwhile;
		}
	}
	wp_reset_query();
	echo json_encode($return);
	die();
}




/* DON'T DELETE THIS CLOSING TAG */ ?>
