<?php

/*
 * Copyright (c) 2024. Philip Kaufmann, Jan Eike Suchard, Benjamin Witte
 * This file is copyright under the latest version of the EUPL.
 * Please see the LICENSE file for your rights.
 */

function ggl_post_type_screening_location(): void {
	register_post_type( 'screening-location',
		[
			'label'               => __( 'Screening Location', 'ggl-post-types' ),
			'labels'              => [
				'menu_name'             => __( 'Screening Locations', 'ggl-post-types' ),
				'name_admin_bar'        => __( 'Screening Location', 'ggl-post-types' ),
				'singular_name'         => __( 'Screening Location', 'ggl-post-types' ),
				'add_new_item'          => __( 'Add Screening Location', 'ggl-post-types' ),
				'add_new'               => __( 'Add Screening Location', 'ggl-post-types' ),
				'edit_item'             => __( 'Edit Screening Location', 'ggl-post-types' ),
				'view_item'             => __( 'View Screening Location', 'ggl-post-types' ),
				'all_items'             => __( 'All Screening Locations', 'ggl-post-types' ),
			],
			'public'              => false,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'can_export'          => true,
			'show_ui'             => current_user_can("manage_options"),
			'show_in_rest'        => true,
			'menu_position'       => 9,
			'menu_icon'           => 'dashicons-location',
			'supports'            => [ 'title' ],
			'rewrite'             => false,
		] );
}

function location_register_meta_boxes( $meta_boxes ) {
	$prefix = 'location_';

	$meta_boxes[] = [
		'title'      => esc_html__( 'Address Details', "ggl-post-types" ),
		'id'         => $prefix . 'address_details',
		'context'    => 'after_title',
		'style'      => 'seamless',
		'post_types' => [ 'screening-location' ],
		'fields'     => [
			[
				'type' => 'heading',
				'name' => esc_html__( 'Address Details', 'ggl-post-types' ),
			],
			[
				'type' => 'text',
				'name' => esc_html__( 'Street', 'ggl-post-types' ),
				'id'   => "street",
				'required' => true,
			],
			[
				'type' => 'text',
				'name' => esc_html__( 'Postal Code', 'ggl-post-types' ),
				'id'   => "postal_code",
				'required' => true,
			],
			[
				'type' => 'text',
				'name' => esc_html__( 'City', 'ggl-post-types' ),
				'id'   => "city",
				'required' => true,

			],
			[
				'type' => 'select_advanced',
				'name' => esc_html__( 'Country', 'ggl-post-types' ),
				'id'   => "country",
				'options'  => generate_country_mapping(),
				'required' => true,
			],
		],
	];

	return $meta_boxes;
}


