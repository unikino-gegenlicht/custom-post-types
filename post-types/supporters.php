<?php
/*
 * Copyright (c) 2024. Philip Kaufmann, Jan Eike Suchard, Benjamin Witte
 * This file is copyright under the latest version of the EUPL.
 * Please see the LICENSE file for your rights.
 */

function ggl_post_type_supporter(): void {
	register_post_type( 'supporter',
		[
			'label'               => __( 'Supporters', 'ggl-post-types' ),
			'labels'              => [
				'menu_name'             => __( 'Supporters', 'ggl-post-types' ),
				'name_admin_bar'        => __( 'Supporter', 'ggl-post-types' ),
				'singular_name'         => __( 'Supporter', 'ggl-post-types' ),
				'add_new_item'          => __( 'Add Supporter', 'ggl-post-types' ),
				'add_new'               => __( 'Add Supporter', 'ggl-post-types' ),
				'edit_item'             => __( 'Edit Supporter', 'ggl-post-types' ),
				'view_item'             => __( 'View Supporter', 'ggl-post-types' ),
				'all_items'             => __( 'All Supporters', 'ggl-post-types' ),
				'featured_image'        => __( 'Logo', 'ggl-post-types' ),
				'set_featured_image'    => __( 'Set Logo', 'ggl-post-types' ),
				'remove_featured_image' => __( 'Remove Logo', 'ggl-post-types' ),
				'upload_featured_image' => __( 'Upload Logo', 'ggl-post-types' ),
			],
			'public'              => true,
			'has_archive'         => 'supporters',
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'can_export'          => true,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'menu_position'       => 9,
			'menu_icon'           => 'dashicons-superhero-alt',
			'supports'            => [ 'title', 'thumbnail', 'editor' ],
			'rewrite'             => [
				'slug'       => 'supporters',
				'with_front' => false,
				'pages'      => false,
			]
		] );
}

function supporter_register_meta_boxes( $meta_boxes ) {
	$prefix = 'supporter_';

	$meta_boxes[] = [
		'title'      => esc_html__( 'Further Information', 'ggl-post-types' ),
		'id'         => 'supporter',
		'context'    => 'side',
		'post_types' => [ 'supporter' ],
		'fields'     => [
			[
				'type' => 'url',
				'name' => esc_html__( 'Website', 'ggl-post-types' ),
				'id'   => $prefix . 'website',
			],
		],
	];

	return $meta_boxes;
}


