<?php

/*
	CPT, AND ALL FUNCTIONS SETTING UP THE THEME'S SPECIFIC DATA STRUCTURE.

*/

add_action('admin_menu','remove_default_post_type');

function remove_default_post_type() {
	remove_menu_page('edit.php');
}

/* Bones Custom Post Type Example
This page walks you through creating 
a custom post type and taxonomies. You
can edit this one or copy the following code 
to create another one. 

I put this in a separate file so as to 
keep it organized. I find it easier to edit
and change things if they are concentrated
in their own file.

Developed by: Eddie Machado
URL: http://themble.com/bones/
*/

// Flush rewrite rules for custom post types
add_action( 'after_switch_theme', 'bones_flush_rewrite_rules' );

// Flush your rewrite rules
function bones_flush_rewrite_rules() {
	flush_rewrite_rules();
}

// let's create the function for the custom type
function cpt_noeud() { 
	// creating (registering) the custom type 
	register_post_type( 'noeud', /* (http://codex.wordpress.org/Function_Reference/register_post_type) */
		// let's now add all the options for this post type
		array( 'labels' => array(
			'name' => __( 'Noeuds de Savoir', 'bonestheme' ), /* This is the Title of the Group */
			'singular_name' => __( 'Noeud', 'bonestheme' ), /* This is the individual type */
			'all_items' => __( 'Tous les noeuds', 'bonestheme' ), /* the all items menu item */
			'add_new' => __( 'Ajouter', 'bonestheme' ), /* The add new menu item */
			'add_new_item' => __( 'Ajouter un nouveau Noeud de Savoir', 'bonestheme' ), /* Add New Display Title */
			'edit' => __( 'Modifier', 'bonestheme' ), /* Edit Dialog */
			'edit_item' => __( 'Modifier les Noeuds de Savoir', 'bonestheme' ), /* Edit Display Title */
			'new_item' => __( 'Nouveau noeud', 'bonestheme' ), /* New Display Title */
			'view_item' => __( 'Voir le Noeud de Savoir', 'bonestheme' ), /* View Display Title */
			'search_items' => __( 'Chercher des Noeuds de Savoir', 'bonestheme' ), /* Search Custom Type Title */ 
			'not_found' =>  __( 'Nothing found in the Database.', 'bonestheme' ), /* This displays if there are no entries yet */ 
			'not_found_in_trash' => __( 'Nothing found in Trash', 'bonestheme' ), /* This displays if there is nothing in the trash */
			'parent_item_colon' => ''
			), /* end of arrays */
			'description' => __( 'Un noeud de savoir à propos des Internets.', 'bonestheme' ), /* Custom Type Description */
			'public' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'show_ui' => true,
			'query_var' => true,
			'menu_position' => 1, /* this is what order you want it to appear in on the left hand side menu */ 
			'menu_icon' => get_stylesheet_directory_uri() . '/library/images/custom-post-icon.png', /* the icon for the custom post type menu */
			'rewrite'	=> array( 'slug' => 'noeud', 'with_front' => false ), /* you can specify its url slug */
			'has_archive' => 'noeuds', /* you can rename the slug here */
			'capability_type' => 'post',
			'hierarchical' => false,
			/* the next one is important, it tells what's enabled in the post editor */
			'supports' => array( 'title', 'editor', 'author', 'thumbnail',  'revisions', 'sticky')
		) /* end of options */
	); /* end of register post type */
	
}

	// adding the function to the Wordpress init
	add_action( 'init', 'cpt_noeud');
	
	/*
	for more information on taxonomies, go here:
	http://codex.wordpress.org/Function_Reference/register_taxonomy
	*/
	
	// now let's add custom categories (these act like categories)
	register_taxonomy( 'fil', 
		array('noeud'), /* if you change the name of register_post_type( 'custom_type', then you have to change this */
		array('hierarchical' => false,     /* if this is true, it acts like categories */
			'labels' => array(
				'name' => __( 'Fils de connexion', 'bonestheme' ), /* name of the custom taxonomy */
				'singular_name' => __( 'Fil', 'bonestheme' ), /* single taxonomy name */
				'search_items' =>  __( 'Chercher les fils', 'bonestheme' ), /* search title for taxomony */
				'all_items' => __( 'Tous les fils', 'bonestheme' ), /* all title for taxonomies */
				'parent_item' => __( 'Parent Custom Category', 'bonestheme' ), /* parent title for taxonomy */
				'parent_item_colon' => __( 'Parent Custom Category:', 'bonestheme' ), /* parent taxonomy title */
				'edit_item' => __( 'Modifier le fil', 'bonestheme' ), /* edit custom taxonomy title */
				'update_item' => __( 'Mettre à jour', 'bonestheme' ), /* update title for taxonomy */
				'add_new_item' => __( 'Ajouter un nouveau fil de connexion', 'bonestheme' ), /* add new title for taxonomy */
				'new_item_name' => __( 'Nouveau fil de connexion', 'bonestheme' ) /* name title for taxonomy */
			),
			'description' => __( 'Un fil permet de connecter un noeud à d\'autres, tissant ainsi la Toile des Internets.', 'bonestheme' ), /* Custom Type Description */
			'show_admin_column' => true, 
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'fil' ),
		)
	);
	
	/*
		looking for custom meta boxes?
		check out this fantastic tool:
		https://github.com/jaredatch/Custom-Metaboxes-and-Fields-for-WordPress
	*/
	

?>
