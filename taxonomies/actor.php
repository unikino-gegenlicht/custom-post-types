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
		'public'         => true,
		'show_in_menu'   => false,
		'meta_box_cb'    => false,
		'show_tag_cloud' => false,
		'query_var'      => 'actor',
		'rewrite'        => false,
		"capabilities"   => [
			"manage_terms" => "publish_posts",
			"edit_terms"   => "publish_posts",
			"assign_terms" => "publish_posts",
			"delete_terms" => "edit_others_posts",
		]
	] );
}