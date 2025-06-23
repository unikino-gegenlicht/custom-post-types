<?php

function ggl_taxonomy_genre(): void {
	register_taxonomy( 'genre', null, [
		'labels'         => [
			'name'          => __( 'Genres', 'ggl-post-types' ),
			'singular_name' => __( 'Genre', 'ggl-post-types' ),
			'search_items'  => __( 'Search Genres', 'ggl-post-types' ),
			'all_items'     => __( 'All Genres', 'ggl-post-types' ),
			'edit_item'     => __( 'Edit Genre', 'ggl-post-types' ),
			'update_item'   => __( 'Update Genre', 'ggl-post-types' ),
			'add_new_item'  => __( 'Add New Genre', 'ggl-post-types' ),
			'new_item_name' => __( 'New Genre', 'ggl-post-types' ),
		],
		'public'         => true,
		'show_in_menu'   => true,
		'meta_box_cb'    => false,
		'show_tag_cloud' => false,
		'query_var'      => 'genre',
		'rewrite'        => false
	] );
}