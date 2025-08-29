<?php

function ggl_taxonomy_director(): void {
	register_taxonomy( 'director', null, [
		'labels'         => [
			'name'          => __( 'Directors', 'ggl-post-types' ),
			'singular_name' => __( 'Director', 'ggl-post-types' ),
			'search_items'  => __( 'Search Directors', 'ggl-post-types' ),
			'all_items'     => __( 'All Directors', 'ggl-post-types' ),
			'edit_item'     => __( 'Edit Director', 'ggl-post-types' ),
			'update_item'   => __( 'Update Director', 'ggl-post-types' ),
			'add_new_item'  => __( 'Add New Director', 'ggl-post-types' ),
			'new_item_name' => __( 'New Director', 'ggl-post-types' ),
		],
		'public'         => true,
		'show_in_menu'   => false,
		'meta_box_cb'    => false,
		'show_tag_cloud' => false,
		'query_var'      => 'director',
		'rewrite'        => false,
		"capabilities"   => [
			"manage_terms" => "publish_posts",
			"edit_terms"   => "publish_posts",
			"assign_terms" => "publish_posts",
			"delete_terms" => "edit_others_posts",
		]
	] );
}