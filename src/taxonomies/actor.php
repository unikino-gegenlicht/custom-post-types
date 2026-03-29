<?php

function ggl_taxonomy_actor(): void {
	register_taxonomy( 'actor', null, [
		'labels'         => [
			'name'          => __( 'Actors', 'ggl-post-types' ),
			'singular_name' => __( 'Actors', 'ggl-post-types' ),
			'search_items'  => __( 'Search Actors', 'ggl-post-types' ),
			'all_items'     => __( 'All Actors', 'ggl-post-types' ),
			'edit_item'     => __( 'Edit Actor', 'ggl-post-types' ),
			'update_item'   => __( 'Update Actor', 'ggl-post-types' ),
			'add_new_item'  => __( 'Add New Actor', 'ggl-post-types' ),
			'new_item_name' => __( 'New Actor', 'ggl-post-types' ),
		],
		'public'         => false,
		'show_in_menu'  => current_user_can( "edit_others_posts" ),
		'show_ui'        => true,
		'show_in_rest'   => true,
		'show_tag_cloud' => false,
		'meta_box_cb'     => false,
		'query_var'      => 'actor',
		'rewrite'        => false,
		"description"    => __( "This taxonomy is used for managing the actors that are displayed for each movie. Please only add a new " ),
		"capabilities"   => [
			"manage_terms" => "publish_posts",
			"edit_terms"   => "publish_posts",
			"assign_terms" => "publish_posts",
			"delete_terms" => "edit_others_posts",
		]
	] );
}